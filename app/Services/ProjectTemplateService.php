<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectTemplate;
use App\Models\Modul;
use App\Models\Blok;
use App\Models\SubBlok;
use App\Models\Itp;
use Illuminate\Support\Facades\DB;

class ProjectTemplateService
{
    /**
     * Clone a template into a new project with all hierarchy data.
     *
     * @param ProjectTemplate $template
     * @param array $projectData  ['nama_project', 'kode_project', 'deskripsi', 'tanggal_kontrak', 'tanggal_mulai', 'deadline']
     * @return Project
     */
    public function cloneTemplate(ProjectTemplate $template, array $projectData): Project
    {
        return DB::transaction(function () use ($template, $projectData) {

            // 1. Create the project
            $project = Project::create([
                'nama_project'    => $projectData['nama_project'],
                'kode_project'    => $projectData['kode_project'],
                'deskripsi'       => $projectData['deskripsi'] ?? null,
                'status'          => 'active',
                'template_id'    => $template->id,
                'tanggal_kontrak' => $projectData['tanggal_kontrak'] ?? null,
                'tanggal_mulai'   => $projectData['tanggal_mulai'] ?? null,
                'deadline'        => $projectData['deadline'] ?? null,
            ]);

            // 2. Load template hierarchy
            $template->load('templateModuls.templateBloks.templateSubBloks.templateItps');

            // 3. Clone modules → blocks → sub-blocks → ITPs
            foreach ($template->templateModuls as $tModul) {
                $modul = Modul::create([
                    'project_id'  => $project->id,
                    'nama_modul'  => $tModul->nama_modul,
                    'deskripsi'   => $tModul->deskripsi,
                ]);

                foreach ($tModul->templateBloks as $tBlok) {
                    $blok = Blok::create([
                        'modul_id'  => $modul->id,
                        'nama_blok' => $tBlok->nama_blok,
                    ]);

                    foreach ($tBlok->templateSubBloks as $tSubBlok) {
                        $subBlok = SubBlok::create([
                            'blok_id'       => $blok->id,
                            'nama_sub_blok' => $tSubBlok->nama_sub_blok,
                        ]);

                        // Batch insert ITPs for performance
                        $itpRows = [];
                        foreach ($tSubBlok->templateItps as $tItp) {
                            $itpRows[] = [
                                'sub_blok_id'          => $subBlok->id,
                                'assembly_code'        => $tItp->assembly_code,
                                'assembly_description' => $tItp->assembly_description,
                                'code'                 => $tItp->code,
                                'item'                 => $tItp->item,
                                'yard_val'             => $tItp->yard_val,
                                'class_val'            => $tItp->class_val,
                                'os_val'               => $tItp->os_val,
                                'stat_val'             => $tItp->stat_val,
                                'created_at'           => now(),
                                'updated_at'           => now(),
                            ];
                        }

                        // Insert in chunks of 100 for performance
                        if (!empty($itpRows)) {
                            foreach (array_chunk($itpRows, 100) as $chunk) {
                                DB::table('itps')->insert($chunk);
                            }
                        }
                    }
                }
            }

            return $project;
        });
    }
}
