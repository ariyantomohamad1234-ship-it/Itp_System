<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ITP Inspection Certificate</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; line-height: 1.5; font-size: 11px; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 3px solid #dc2626; padding-bottom: 15px; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 24px; text-transform: uppercase; color: #dc2626; letter-spacing: 1px; }
        .header p { margin: 5px 0 0; font-size: 11px; color: #64748b; font-weight: bold; }
        
        .certificate-info { width: 100%; margin-bottom: 25px; }
        .certificate-info td { padding: 6px 0; }
        .label { font-weight: bold; width: 130px; color: #475569; font-size: 10px; text-transform: uppercase; }
        
        .section-title { background: #fee2e2; color: #991b1b; padding: 6px 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 15px; border-left: 5px solid #dc2626; font-size: 11px; letter-spacing: 0.5px; }
        
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        table.data-table th, table.data-table td { border: 1px solid #e2e8f0; padding: 12px; text-align: left; }
        table.data-table th { background: #f8fafc; font-weight: bold; color: #475569; font-size: 10px; text-transform: uppercase; }
        
        .photo-container { text-align: center; margin-top: 25px; }
        .photo-container img { max-width: 100%; max-height: 350px; border: 2px solid #f1f5f9; border-radius: 8px; }
        
        .footer { margin-top: 60px; width: 100%; }
        .signature { text-align: center; width: 33%; }
        .signature-line { border-top: 1px solid #94a3b8; width: 140px; margin: 50px auto 8px; }
        .signature-role { font-size: 9px; font-weight: bold; text-transform: uppercase; color: #64748b; }
        .signature-name { font-weight: bold; font-size: 11px; color: #1e293b; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Inspection and Test Plan Certificate</h1>
        <p>Integrated Ship Construction Monitoring System</p>
    </div>

    <table class="certificate-info">
        <tr>
            <td class="label">Project Name</td>
            <td>: {{ $project->nama_project ?? '-' }}</td>
            <td class="label">Date Issued</td>
            <td>: {{ now()->format('d M Y H:i') }}</td>
        </tr>
        <tr>
            <td class="label">Project Code</td>
            <td>: {{ $project->kode_project ?? '-' }}</td>
            <td class="label">Certificate ID</td>
            <td>: #CERT-ITP-{{ $itpData->id }}</td>
        </tr>
    </table>

    <div class="section-title">Location & Identification</div>
    <table class="certificate-info">
        <tr>
            <td class="label">Module</td>
            <td>: {{ $project->nama_modul ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Block / Sub-Block</td>
            <td>: {{ $project->nama_blok ?? '-' }} / {{ $project->nama_sub_blok ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">ITP Code</td>
            <td>: {{ $itp->code }}</td>
        </tr>
    </table>

    <div class="section-title">Inspection Results</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Inspection Item</th>
                <th>Result / Note</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $itp->description }}</td>
                <td>{{ $itpData->keterangan ?: '(No notes provided)' }}</td>
                <td style="color: green; font-weight: bold; text-transform: uppercase;">{{ $itpData->status }}</td>
            </tr>
        </tbody>
    </table>

    @if($itpData->photo)
    <div class="section-title">Evidence Photo</div>
    <div class="photo-container">
        <!-- In dompdf, use public_path for local images -->
        <img src="{{ public_path('storage/' . $itpData->photo) }}" alt="Inspection Evidence">
    </div>
    @endif

    <div class="footer">
        <table width="100%">
            <tr>
                <td class="signature">
                    <div class="signature-role">Uploader (Yard)</div>
                    <div class="signature-line"></div>
                    <div>{{ $itpData->uploader->name ?? '-' }}</div>
                </td>
                <td class="signature">
                    <div class="signature-role">Approver</div>
                    <div class="signature-line"></div>
                    <div>(Digitally Verified)</div>
                </td>
                <td class="signature">
                    <div class="signature-role">Authorized Representative</div>
                    <div class="signature-line"></div>
                    <div>Quality Assurance</div>
                </td>
            </tr>
        </table>
    </div>

    <p style="font-size: 8px; color: #999; margin-top: 30px; text-align: center;">
        This document is automatically generated by the ITP Monitoring System and is valid without a physical signature.
    </p>
</body>
</html>
