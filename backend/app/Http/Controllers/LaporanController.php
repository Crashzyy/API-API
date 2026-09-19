<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use ZipArchive;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $peminjamans = $this->getPeminjamans($filters);

        return view('laporan.index', [
            'peminjamans' => $peminjamans,
            ...$filters,
        ]);
    }

    public function excel(Request $request)
    {
        $filters = $this->filters($request);
        $rows = $this->reportRows($this->getPeminjamans($filters));
        $headers = $this->reportHeaders();
        $filePath = tempnam(sys_get_temp_dir(), 'laporan-');

        $zip = new ZipArchive();
        if ($zip->open($filePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('File Excel tidak dapat dibuat.');
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml());
        $zip->addFromString('_rels/.rels', $this->rootRelationshipsXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelationshipsXml());
        $zip->addFromString('xl/styles.xml', $this->stylesXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->worksheetXml(
            array_values($headers),
            array_map(fn (array $row) => array_values($row), $rows)
        ));
        $zip->close();

        return response()
            ->download($filePath, 'laporan-peminjaman.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->deleteFileAfterSend(true);
    }

    public function pdf(Request $request)
    {
        $filters = $this->filters($request);
        $rows = $this->reportRows($this->getPeminjamans($filters));

        return Pdf::loadView('laporan.pdf', [
            'headers' => $this->reportHeaders(),
            'rows' => $rows,
            ...$filters,
        ])
            ->setPaper('a3', 'landscape')
            ->download('laporan-peminjaman.pdf');
    }

    private function filters(Request $request): array
    {
        return array_merge([
            'status' => null,
            'mulai' => null,
            'selesai' => null,
        ], $request->validate([
            'status' => ['nullable', 'in:diajukan,dipinjam,selesai,telat'],
            'mulai' => ['nullable', 'date'],
            'selesai' => ['nullable', 'date'],
        ]));
    }

    private function getPeminjamans(array $filters)
    {
        return Peminjaman::with([
            'user',
            'detailPinjams.alat.kategori',
            'pengembalian.petugas',
        ])
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['mulai'] ?? null, fn ($query, $mulai) => $query->whereDate('tgl_pinjam', '>=', $mulai))
            ->when($filters['selesai'] ?? null, fn ($query, $selesai) => $query->whereDate('tgl_pinjam', '<=', $selesai))
            ->latest('tgl_pinjam')
            ->get();
    }

    private function reportHeaders(): array
    {
        return [
            'no' => 'No',
            'peminjaman_id' => 'ID Peminjaman',
            'detail_id' => 'ID Detail',
            'peminjam' => 'Nama Peminjam',
            'email' => 'Email',
            'no_hp' => 'No. HP',
            'alamat' => 'Alamat',
            'alat' => 'Nama Alat',
            'kategori' => 'Kategori Alat',
            'status_kondisi' => 'Kondisi Alat',
            'deskripsi_alat' => 'Deskripsi Alat',
            'jumlah' => 'Jumlah',
            'tgl_pinjam' => 'Tanggal Pinjam',
            'tgl_kembali_plan' => 'Rencana Kembali',
            'status' => 'Status Peminjaman',
            'tgl_kembali' => 'Tanggal Dikembalikan',
            'kondisi_kembali' => 'Kondisi Saat Kembali',
            'denda' => 'Denda',
            'petugas' => 'Petugas Pengembalian',
        ];
    }

    private function reportRows($peminjamans): array
    {
        $rows = [];
        $number = 1;

        foreach ($peminjamans as $peminjaman) {
            $user = $peminjaman->user;
            $pengembalian = $peminjaman->pengembalian;
            $details = $peminjaman->detailPinjams;

            if ($details->isEmpty()) {
                $rows[] = $this->makeReportRow($number++, $peminjaman, $user, null, $pengembalian);
                continue;
            }

            foreach ($details as $detail) {
                $rows[] = $this->makeReportRow($number++, $peminjaman, $user, $detail, $pengembalian);
            }
        }

        return $rows;
    }

    private function makeReportRow($number, $peminjaman, $user, $detail, $pengembalian): array
    {
        $alat = $detail?->alat;

        return [
            'no' => $number,
            'peminjaman_id' => $peminjaman->id,
            'detail_id' => $detail?->id ?? '-',
            'peminjam' => $user?->name ?? '-',
            'email' => $user?->email ?? '-',
            'no_hp' => $user?->no_hp ?? '-',
            'alamat' => $user?->alamat ?? '-',
            'alat' => $alat?->nama_alat ?? '-',
            'kategori' => $alat?->kategori?->nama_kategori ?? '-',
            'status_kondisi' => $alat?->status_kondisi ?? '-',
            'deskripsi_alat' => $alat?->deskripsi ?? '-',
            'jumlah' => $detail?->jumlah ?? '-',
            'tgl_pinjam' => optional($peminjaman->tgl_pinjam)->format('d-m-Y') ?? '-',
            'tgl_kembali_plan' => optional($peminjaman->tgl_kembali_plan)->format('d-m-Y') ?? '-',
            'status' => ucfirst($peminjaman->status ?? '-'),
            'tgl_kembali' => optional($pengembalian?->tgl_kembali)->format('d-m-Y') ?? '-',
            'kondisi_kembali' => $pengembalian?->kondisi_kembali ?? '-',
            'denda' => $pengembalian?->denda ?? 0,
            'petugas' => $pengembalian?->petugas?->name ?? '-',
        ];
    }

    private function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';
    }

    private function rootRelationshipsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private function workbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="Laporan Peminjaman" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    private function workbookRelationshipsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
    }

    private function stylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="2"><font><sz val="10"/><color rgb="FF000000"/><name val="Arial"/></font><font><b/><sz val="10"/><color rgb="FFFFFFFF"/><name val="Arial"/></font></fonts>'
            . '<fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF1F2937"/><bgColor indexed="64"/></patternFill></fill></fills>'
            . '<borders count="2"><border><left/><right/><top/><bottom/><diagonal/></border><border><left style="thin"><color rgb="FFD1D5DB"/></left><right style="thin"><color rgb="FFD1D5DB"/></right><top style="thin"><color rgb="FFD1D5DB"/></top><bottom style="thin"><color rgb="FFD1D5DB"/></bottom><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="3"><xf numFmtId="0" fontId="0" fillId="0" borderId="1" applyAlignment="1"><alignment vertical="top"/></xf><xf numFmtId="0" fontId="1" fillId="2" borderId="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf><xf numFmtId="0" fontId="0" fillId="0" borderId="1" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf></cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>';
    }

    private function worksheetXml(array $headers, array $rows): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<cols>';

        for ($column = 1; $column <= count($headers); $column++) {
            $xml .= '<col min="' . $column . '" max="' . $column . '" width="20" customWidth="1"/>';
        }

        $xml .= '</cols><sheetData>';
        $xml .= $this->worksheetRow(1, $headers, true);

        foreach ($rows as $rowNumber => $row) {
            $xml .= $this->worksheetRow($rowNumber + 2, $row);
        }

        return $xml . '</sheetData><autoFilter ref="A1:' . $this->excelColumn(count($headers)) . max(1, count($rows) + 1) . '"/>'
            . '<pageMargins left="0.25" right="0.25" top="0.5" bottom="0.5" header="0.3" footer="0.3"/>'
            . '</worksheet>';
    }

    private function worksheetRow(int $rowNumber, array $values, bool $header = false): string
    {
        $xml = '<row r="' . $rowNumber . '">';

        foreach (array_values($values) as $index => $value) {
            $cellReference = $this->excelColumn($index + 1) . $rowNumber;
            $style = $header ? 1 : 2;
            $escaped = htmlspecialchars((string) $value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
            $xml .= '<c r="' . $cellReference . '" s="' . $style . '" t="inlineStr"><is><t xml:space="preserve">' . $escaped . '</t></is></c>';
        }

        return $xml . '</row>';
    }

    private function excelColumn(int $number): string
    {
        $column = '';

        while ($number > 0) {
            $remainder = ($number - 1) % 26;
            $column = chr(65 + $remainder) . $column;
            $number = intdiv($number - 1, 26);
        }

        return $column;
    }
}
