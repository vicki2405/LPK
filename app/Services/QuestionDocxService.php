<?php

namespace App\Services;

use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\QuestionCategory;
use App\Models\QuestionOption;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\Element\Cell;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\JcTable;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Style\Font;
use ZipArchive;

class QuestionDocxService
{
    /**
     * Hasilkan file Template Word (.docx) resmi standar LPK berbasis Tabel Kartu Soal.
     *
     * @return string Path absolut ke file docx sementara
     */
    public function generateTemplate(): string
    {
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Calibri');
        $phpWord->setDefaultFontSize(11);

        $section = $phpWord->addSection([
            'marginTop' => 1000,
            'marginBottom' => 1000,
            'marginLeft' => 1200,
            'marginRight' => 1200,
        ]);

        // 1. Kop & Judul Dokumen Template
        $section->addText(
            'LPK NIHON GO CBT - TEMPLATE SOAL UJIAN RESMI (.DOCX)',
            ['name' => 'Calibri', 'size' => 15, 'bold' => true, 'color' => '1E3A8A'],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 120]
        );

        $section->addText(
            'Panduan untuk Sensei: Dokumen ini adalah naskah bank soal murni untuk ujian CBT. Setiap 1 butir soal diletakkan dalam 1 TABEL KARTU di bawah ini. Pilihan jawaban bersifat fleksibel: standar 4 opsi (A–D), dapat ditambah hingga OPSI E (5 opsi), atau dikurangi menjadi 2–3 opsi (misal hanya OPSI A–B untuk soal Benar/Salah ○×). Gambar ilustrasi dapat langsung di-paste ke dalam sel "SOAL".',
            ['name' => 'Calibri', 'size' => 9.5, 'italic' => true, 'color' => '475569'],
            ['alignment' => Jc::BOTH, 'spaceAfter' => 240]
        );

        // Gaya Tabel
        $tableStyle = [
            'borderColor' => '94A3B8',
            'borderSize' => 6,
            'cellMargin' => 80,
            'alignment' => JcTable::CENTER,
        ];
        $headerCellStyle = ['bgColor' => '1E293B'];
        $labelCellStyle = ['bgColor' => 'F1F5F9', 'valign' => 'center'];
        $contentCellStyle = ['bgColor' => 'FFFFFF', 'valign' => 'center'];

        $headerFont = ['bold' => true, 'color' => 'FFFFFF', 'size' => 10];
        $labelFont = ['bold' => true, 'color' => '334155', 'size' => 10];
        $jpFont = ['name' => 'Meiryo', 'size' => 11, 'color' => '0F172A'];

        // Daftar Contoh Soal Template CBT Murni (Tanpa Kolom Pembahasan)
        $samples = [
            [
                'no' => '1',
                'section' => 'moji_goi',
                'instruction' => '_____の ことばは どう よみますか。',
                'passage' => '',
                'question' => 'わたしは まいあさ 新聞を よみます。',
                'optA' => 'しんぶん',
                'optB' => 'ほんぶん',
                'optC' => 'しんぼん',
                'optD' => 'きんぶん',
                'key' => 'A',
            ],
            [
                'no' => '2',
                'section' => 'bunpou',
                'instruction' => '( ) に なにを いれますか。いちばん いい ものを ひとつ えらんで ください。',
                'passage' => '',
                'question' => 'つくえの うえ ( ) ほんが あります。',
                'optA' => 'に',
                'optB' => 'で',
                'optC' => 'を',
                'optD' => 'が',
                'key' => 'A',
            ],
            [
                'no' => '3',
                'section' => 'dokkai',
                'instruction' => 'つぎの ぶんしょうを よんで、しつもんに こたえて ください。',
                'passage' => "きのう わたしは ともだちと デパートへ いきました。\nシャツと くつを かいました。\nそれから、レストランで おいしい ラーメンを たべました。",
                'question' => 'この ひとは レストランで なにを たべましたか。',
                'optA' => 'ラーメン',
                'optB' => 'すし',
                'optC' => 'てんぷら',
                'optD' => 'パン',
                'key' => 'A',
            ],
            [
                'no' => '4',
                'section' => 'moji_goi',
                'instruction' => 'えを みて、ただしい こたえを えらんで ください。',
                'passage' => '',
                'question' => 'この えの たてものは なんですか。(Contoh soal bergambar: Sensei dapat menempelkan gambar denah/situasi langsung di sini)',
                'optA' => 'ぎんこう (Bank)',
                'optB' => 'びょういん (Rumah Sakit)',
                'optC' => 'がっこう (Sekolah)',
                'optD' => 'えき (Stasiun)',
                'key' => 'C',
            ],
        ];

        foreach ($samples as $s) {
            $table = $section->addTable($tableStyle);

            // Baris 1: Header / Metadata Soal
            $table->addRow();
            $cellHeader = $table->addCell(8500, array_merge($headerCellStyle, ['gridSpan' => 2]));
            $cellHeader->addText(
                "SOAL NO: {$s['no']}   |   SEKSI: {$s['section']}",
                $headerFont,
                ['spaceBefore' => 40, 'spaceAfter' => 40]
            );

            // Baris 2: Petunjuk Soal (Opsional)
            if (!empty($s['instruction'])) {
                $table->addRow();
                $table->addCell(2200, $labelCellStyle)->addText('PETUNJUK', $labelFont);
                $this->addMultilineText($table->addCell(6300, $contentCellStyle), $s['instruction'], $jpFont);
            }

            // Baris 3: Wacana / Teks Dokkai (Jika ada)
            if (!empty($s['passage'])) {
                $table->addRow();
                $table->addCell(2200, $labelCellStyle)->addText('WACANA / BACAAN', $labelFont);
                $this->addMultilineText($table->addCell(6300, $contentCellStyle), $s['passage'], $jpFont);
            }

            // Baris 4: Teks Pertanyaan (Soal + Gambar jika ada)
            $table->addRow();
            $table->addCell(2200, $labelCellStyle)->addText('SOAL / PERTANYAAN', $labelFont);
            $qCell = $table->addCell(6300, $contentCellStyle);
            $this->addMultilineText($qCell, $s['question'], $jpFont);

            // Baris 5-8: Opsi A - D
            $options = [
                ['label' => 'OPSI A', 'val' => $s['optA']],
                ['label' => 'OPSI B', 'val' => $s['optB']],
                ['label' => 'OPSI C', 'val' => $s['optC']],
                ['label' => 'OPSI D', 'val' => $s['optD']],
            ];
            foreach ($options as $opt) {
                $table->addRow();
                $table->addCell(2200, $labelCellStyle)->addText($opt['label'], $labelFont);
                $table->addCell(6300, $contentCellStyle)->addText($opt['val'], $jpFont);
            }

            // Baris 9: Kunci Jawaban
            $table->addRow();
            $table->addCell(2200, $labelCellStyle)->addText('KUNCI JAWABAN', array_merge($labelCellStyle, ['bgColor' => 'FEF3C7']));
            $table->addCell(6300, $contentCellStyle)->addText($s['key'], ['bold' => true, 'color' => 'B45309', 'size' => 11]);

            $section->addTextBreak(1);
        }

        $tempPath = storage_path('app/temp_template_' . uniqid() . '.docx');
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempPath);

        return $tempPath;
    }

    /**
     * Ekspor seluruh butir soal dalam suatu Paket Bank Soal menjadi dokumen Word (.docx) siap cetak.
     *
     * @param QuestionBank $package
     * @return string Path absolut file docx hasil ekspor
     */
    public function exportPackageToDocx(QuestionBank $package): string
    {
        $package->load([
            'subject',
            'questions' => function ($q) {
                $q->with('options')
                  ->orderBy('question_bank_questions.order_index', 'asc')
                  ->orderBy('questions.id', 'asc');
            },
        ]);

        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Calibri');
        $phpWord->setDefaultFontSize(11);

        $section = $phpWord->addSection([
            'marginTop' => 1000,
            'marginBottom' => 1000,
            'marginLeft' => 1200,
            'marginRight' => 1200,
        ]);

        // Judul Paket Soal
        $section->addText(
            mb_strtoupper($package->title),
            ['name' => 'Calibri', 'size' => 16, 'bold' => true, 'color' => '1E3A8A'],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 60]
        );

        $mapelName = $package->subject?->name ?? 'Bahasa Jepang';
        $section->addText(
            "Mata Pelajaran: {$mapelName}   |   Level: {$package->level}   |   Kode: {$package->code}   |   Total Soal: " . $package->questions->count(),
            ['name' => 'Calibri', 'size' => 10, 'italic' => true, 'color' => '475569'],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 240]
        );

        $tableStyle = [
            'borderColor' => 'CBD5E1',
            'borderSize' => 6,
            'cellMargin' => 80,
            'alignment' => JcTable::CENTER,
        ];
        $headerCellStyle = ['bgColor' => '334155'];
        $labelCellStyle = ['bgColor' => 'F8FAFC', 'valign' => 'center'];
        $contentCellStyle = ['bgColor' => 'FFFFFF', 'valign' => 'center'];

        $headerFont = ['bold' => true, 'color' => 'FFFFFF', 'size' => 10];
        $labelFont = ['bold' => true, 'color' => '334155', 'size' => 10];
        $jpFont = ['name' => 'Meiryo', 'size' => 11, 'color' => '0F172A'];

        foreach ($package->questions as $idx => $q) {
            $no = $idx + 1;
            $table = $section->addTable($tableStyle);

            // Baris Header
            $table->addRow();
            $cellHeader = $table->addCell(8500, array_merge($headerCellStyle, ['gridSpan' => 2]));
            $indicatorHeader = $q->category ? "   |   INDIKATOR: " . htmlspecialchars($q->category->name, ENT_NOQUOTES, 'UTF-8') : '';
            $cellHeader->addText(
                "SOAL NO: {$no}   |   SEKSI: {$q->section_type}{$indicatorHeader}   |   BOBOT POIN: {$q->score_points}",
                $headerFont,
                ['spaceBefore' => 40, 'spaceAfter' => 40]
            );

            // Petunjuk
            if (!empty($q->instruction)) {
                $table->addRow();
                $table->addCell(2200, $labelCellStyle)->addText('PETUNJUK', $labelFont);
                $this->addMultilineText($table->addCell(6300, $contentCellStyle), $this->safeDocxText($q->instruction), $jpFont);
            }

            // Wacana
            if (!empty($q->reading_passage)) {
                $table->addRow();
                $table->addCell(2200, $labelCellStyle)->addText('WACANA / BACAAN', $labelFont);
                $this->addMultilineText($table->addCell(6300, $contentCellStyle), $this->safeDocxText($q->reading_passage), $jpFont);
            }

            // Pertanyaan & Gambar
            $table->addRow();
            $table->addCell(2200, $labelCellStyle)->addText('SOAL / PERTANYAAN', $labelFont);
            $qCell = $table->addCell(6300, $contentCellStyle);
            $this->addMultilineText($qCell, $this->safeDocxText($q->question_text), $jpFont);

            // Sisipkan Gambar jika ada di storage publik
            if (!empty($q->image_url)) {
                $localImagePath = public_path(ltrim($q->image_url, '/'));
                if (file_exists($localImagePath)) {
                    $this->addImageToCell($qCell, $localImagePath);
                }
            }

            // Keterangan Audio Choukai jika ada
            if (!empty($q->audio_url)) {
                $table->addRow();
                $table->addCell(2200, $labelCellStyle)->addText('AUDIO CHOUKAI', $labelFont);
                $table->addCell(6300, $contentCellStyle)->addText('🎧 File Audio: ' . basename($q->audio_url), [
                    'italic' => true,
                    'color' => 'BE185D',
                    'size' => 9.5,
                ]);
            }

            // Opsi Jawaban
            $correctKey = 'A';
            foreach ($q->options as $opt) {
                if ($opt->is_correct) {
                    $correctKey = $opt->option_key;
                }
                $table->addRow();
                $table->addCell(2200, $labelCellStyle)->addText('OPSI ' . $opt->option_key, $labelFont);
                $table->addCell(6300, $contentCellStyle)->addText($this->safeDocxText($opt->option_text), $jpFont);
            }

            // Kunci Jawaban
            $table->addRow();
            $table->addCell(2200, $labelCellStyle)->addText('KUNCI JAWABAN', array_merge($labelCellStyle, ['bgColor' => 'FEF3C7']));
            $table->addCell(6300, $contentCellStyle)->addText($correctKey, ['bold' => true, 'color' => 'B45309', 'size' => 11]);

            $section->addTextBreak(1);
        }

        $filename = 'Export_Soal_' . Str::slug($package->code) . '_' . date('Ymd_His') . '.docx';
        $tempPath = storage_path('app/' . $filename);
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempPath);

        return $tempPath;
    }

    /**
     * Impor butir-butir soal langsung dari file Word (.docx) berbasis Tabel.
     *
     * @param UploadedFile $file
     * @param QuestionBank $package
     * @param int|null $userId
     * @return array
     */
    public function importFromDocx(UploadedFile $file, QuestionBank $package, ?int $userId = null): array
    {
        $userId = $userId ?? Auth::id();
        $zipPath = $file->getRealPath();

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new Exception("File Word (.docx) tidak valid atau rusak (gagal membuka arsip zip).");
        }

        // 1. Ekstrak peta relasi media (gambar) dari word/_rels/document.xml.rels
        $imageRels = [];
        $relsXmlContent = $zip->getFromName('word/_rels/document.xml.rels');
        if ($relsXmlContent !== false) {
            $domRels = new DOMDocument();
            @$domRels->loadXML($relsXmlContent);
            $relNodes = $domRels->getElementsByTagName('Relationship');
            foreach ($relNodes as $rel) {
                if (!($rel instanceof DOMElement)) {
                    continue;
                }
                $type = $rel->getAttribute('Type');
                if (str_contains($type, 'image')) {
                    $id = $rel->getAttribute('Id');
                    $target = $rel->getAttribute('Target');
                    // Target bisa berupa "media/image1.png"
                    $imageRels[$id] = 'word/' . ltrim($target, '/');
                }
            }
        }

        // 2. Baca isi teks utama dari word/document.xml
        $docXmlContent = $zip->getFromName('word/document.xml');
        if ($docXmlContent === false) {
            $zip->close();
            throw new Exception("File Word tidak memuat dokumen utama (word/document.xml).");
        }

        // Sanitasi ampersand bebas yang tidak ter-escape agar XML parsing selalu valid
        $docXmlContent = preg_replace('/&(?![a-zA-Z0-9#]+;)/u', '&amp;', $docXmlContent);

        $dom = new DOMDocument();
        @$dom->loadXML($docXmlContent);
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $xpath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
        $xpath->registerNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
        $xpath->registerNamespace('v', 'urn:schemas-microsoft-com:vml');

        // Temukan seluruh tabel (1 tabel = 1 soal)
        $tables = $xpath->query('//w:tbl');
        if (!$tables || $tables->length === 0) {
            $zip->close();
            throw new Exception("Tidak ditemukan tabel soal dalam dokumen Word. Pastikan Anda menggunakan format Template Soal resmi LPK.");
        }

        $totalQuestionsFound = $tables->length;
        $importedCount = 0;
        $errors = [];

        // Hitung index urutan awal dalam question bank
        $currentMaxOrder = DB::table('question_bank_questions')
            ->where('question_bank_id', $package->id)
            ->max('order_index') ?? 0;

        foreach ($tables as $tableIndex => $tableNode) {
            $itemNumber = $tableIndex + 1;
            try {
                $parsedItem = $this->parseSingleTableNode($tableNode, $xpath, $zip, $imageRels);

                if (empty($parsedItem['question_text'])) {
                    $errors[] = "Soal #{$itemNumber}: Teks pertanyaan kosong atau tidak terbaca.";
                    continue;
                }

                if (count($parsedItem['options']) < 2) {
                    $errors[] = "Soal #{$itemNumber}: Opsi jawaban kurang dari 2.";
                    continue;
                }

                // Masukkan ke database dalam transaksi
                DB::transaction(function () use ($parsedItem, $package, $userId, &$currentMaxOrder) {
                    $currentMaxOrder++;

                    // Resolusi Indikator Capaian (QuestionCategory)
                    $categoryId = null;
                    if (!empty($parsedItem['indicator'])) {
                        $cat = QuestionCategory::where('code', $parsedItem['indicator'])
                            ->orWhere('name', 'like', '%' . $parsedItem['indicator'] . '%')
                            ->first();
                        if ($cat) {
                            $categoryId = $cat->id;
                        }
                    }

                    // Fallback cerdas: cari indikator aktif berdasarkan level dan seksi
                    if (!$categoryId) {
                        $fallbackCat = QuestionCategory::where('section_type', $parsedItem['section_type'] ?? 'moji_goi')
                            ->where(function ($q) use ($package) {
                                $pkgLevel = $package->level ?? 'N4';
                                $q->where('level', $pkgLevel)->orWhere('level_code', $pkgLevel);
                            })
                            ->first();
                        if ($fallbackCat) {
                            $categoryId = $fallbackCat->id;
                        }
                    }

                    $question = Question::create([
                        'created_by' => $userId,
                        'question_category_id' => $categoryId,
                        'level' => $package->level ?? 'N4',
                        'section_type' => $parsedItem['section_type'] ?? 'moji_goi',
                        'instruction' => $parsedItem['instruction'] ?? null,
                        'reading_passage' => $parsedItem['reading_passage'] ?? null,
                        'question_text' => $parsedItem['question_text'],
                        'image_url' => $parsedItem['image_url'] ?? null,
                        'score_points' => $parsedItem['score_points'] ?? 1.0,
                        'explanation' => $parsedItem['explanation'] ?? null,
                    ]);

                    $correctKey = strtoupper(trim($parsedItem['correct_key'] ?? 'A'));

                    foreach ($parsedItem['options'] as $idx => $opt) {
                        $key = $opt['key'] ?? chr(65 + $idx);
                        $isCorrect = ($key === $correctKey);

                        QuestionOption::create([
                            'question_id' => $question->id,
                            'option_key' => $key,
                            'option_text' => $opt['text'],
                            'is_correct' => $isCorrect,
                        ]);
                    }

                    // Tautkan ke Question Bank
                    DB::table('question_bank_questions')->insert([
                        'question_bank_id' => $package->id,
                        'question_id' => $question->id,
                        'section_type' => $question->section_type,
                        'order_index' => $currentMaxOrder,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                });

                $importedCount++;
            } catch (Exception $e) {
                Log::warning("Gagal parse soal tabel #{$itemNumber}: " . $e->getMessage());
                $errors[] = "Soal #{$itemNumber}: " . $e->getMessage();
            }
        }

        $zip->close();

        return [
            'success' => $importedCount > 0,
            'total_tables' => $totalQuestionsFound,
            'imported_count' => $importedCount,
            'failed_count' => count($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Parse 1 node Tabel Word menjadi array atribut soal terstruktur.
     */
    protected function parseSingleTableNode(DOMElement $tableNode, DOMXPath $xpath, ZipArchive $zip, array $imageRels): array
    {
        $rows = $xpath->query('.//w:tr', $tableNode);
        $data = [
            'section_type' => 'moji_goi',
            'score_points' => 1.0,
            'indicator' => null,
            'instruction' => null,
            'reading_passage' => null,
            'question_text' => '',
            'image_url' => null,
            'options' => [],
            'correct_key' => 'A',
            'explanation' => null,
        ];

        foreach ($rows as $row) {
            $cells = $xpath->query('.//w:tc', $row);
            if (!$cells || $cells->length === 0) {
                continue;
            }

            // Jika baris header (1 cell lebar dengan SOAL NO ...)
            if ($cells->length === 1) {
                $headerText = $this->extractCellText($cells->item(0), $xpath);
                $this->parseHeaderMetadata($headerText, $data);
                continue;
            }

            // Jika 2 cells: cell 0 = Label, cell 1 = Isi
            $label = strtoupper(trim($this->extractCellText($cells->item(0), $xpath)));
            $contentCell = $cells->item(1);
            $contentText = trim($this->extractCellText($contentCell, $xpath));

            if (str_contains($label, 'PETUNJUK') || str_contains($label, 'INSTRUCTION')) {
                $data['instruction'] = $contentText;
            } elseif (str_contains($label, 'WACANA') || str_contains($label, 'BACAAN') || str_contains($label, 'PASSAGE')) {
                $data['reading_passage'] = $contentText;
            } elseif (str_contains($label, 'INDIKATOR') || str_contains($label, 'KATEGORI') || str_contains($label, 'CAPAIAN') || str_contains($label, 'INDICATOR')) {
                $data['indicator'] = $contentText;
            } elseif (str_contains($label, 'SOAL') || str_contains($label, 'PERTANYAAN') || str_contains($label, 'QUESTION')) {
                $data['question_text'] = $contentText;

                // Cari apakah ada gambar dalam cell pertanyaan
                $imageUrl = $this->extractImageFromCell($contentCell, $xpath, $zip, $imageRels);
                if ($imageUrl) {
                    $data['image_url'] = $imageUrl;
                }
            } elseif (preg_match('/OPSI\s*([A-E])/i', $label, $m)) {
                $key = strtoupper($m[1]);
                $data['options'][] = [
                    'key' => $key,
                    'text' => $contentText,
                ];
            } elseif (str_contains($label, 'KUNCI') || str_contains($label, 'JAWABAN') || str_contains($label, 'ANSWER')) {
                if (preg_match('/([A-E])/i', $contentText, $km)) {
                    $data['correct_key'] = strtoupper($km[1]);
                }
            } elseif (str_contains($label, 'PEMBAHASAN') || str_contains($label, 'KAITSU') || str_contains($label, 'PENJELASAN')) {
                $data['explanation'] = $contentText;
            } elseif (str_contains($label, 'NO') || str_contains($label, 'SEKSI')) {
                // Alternatif header baris 2 kolom
                $this->parseHeaderMetadata($label . ' ' . $contentText, $data);
            }
        }

        return $data;
    }

    /**
     * Ekstrak teks dari sebuah cell XML Word, dengan mengonversi phonetic guide / ruby furigana.
     */
    protected function extractCellText(DOMElement $cell, DOMXPath $xpath): string
    {
        $paragraphs = $xpath->query('.//w:p', $cell);
        $pTexts = [];

        foreach ($paragraphs as $p) {
            $lineText = '';
            $childNodeList = $p->childNodes;
            foreach ($childNodeList as $child) {
                if ($child->nodeName === 'w:r') {
                    // Cek teks biasa
                    $tNodes = $xpath->query('.//w:t', $child);
                    foreach ($tNodes as $t) {
                        $lineText .= $t->nodeValue;
                    }
                } elseif ($child->nodeName === 'w:ruby') {
                    // Konversi <w:ruby> ke <ruby>Kanji<rt>furigana</rt></ruby>
                    $baseText = '';
                    $rtText = '';

                    $baseNodes = $xpath->query('.//w:rubyBase//w:t', $child);
                    foreach ($baseNodes as $b) {
                        $baseText .= $b->nodeValue;
                    }

                    $rtNodes = $xpath->query('.//w:rt//w:t', $child);
                    foreach ($rtNodes as $r) {
                        $rtText .= $r->nodeValue;
                    }

                    if (!empty($baseText) && !empty($rtText)) {
                        $lineText .= "<ruby>{$baseText}<rt>{$rtText}</rt></ruby>";
                    } else {
                        $lineText .= $baseText . $rtText;
                    }
                }
            }
            if (trim($lineText) !== '') {
                $pTexts[] = trim($lineText);
            }
        }

        return implode("\n", $pTexts);
    }

    /**
     * Ekstrak file gambar yang ada di dalam cell Word, simpan ke storage publik.
     */
    protected function extractImageFromCell(DOMElement $cell, DOMXPath $xpath, ZipArchive $zip, array $imageRels): ?string
    {
        // Cari elemen drawing blip (OpenXML DrawingML)
        $blipNodes = $xpath->query('.//a:blip', $cell);
        $relId = null;

        if ($blipNodes && $blipNodes->length > 0) {
            $firstBlip = $blipNodes->item(0);
            if ($firstBlip instanceof DOMElement) {
                $relId = $firstBlip->getAttribute('r:embed');
            }
        }

        // Alternatif VML legacy <v:imagedata r:id="...">
        if (!$relId) {
            $vmlNodes = $xpath->query('.//v:imagedata', $cell);
            if ($vmlNodes && $vmlNodes->length > 0) {
                $firstVml = $vmlNodes->item(0);
                if ($firstVml instanceof DOMElement) {
                    $relId = $firstVml->getAttribute('r:id');
                }
            }
        }

        if (!$relId || !isset($imageRels[$relId])) {
            return null;
        }

        $imageZipPath = $imageRels[$relId];
        $binaryData = $zip->getFromName($imageZipPath);
        if ($binaryData === false) {
            return null;
        }

        $ext = pathinfo($imageZipPath, PATHINFO_EXTENSION);
        if (empty($ext)) {
            $ext = 'png';
        }

        $filename = 'docx_img_' . uniqid() . '.' . strtolower($ext);
        $storageRelativePath = 'questions/images/' . $filename;

        Storage::disk('public')->put($storageRelativePath, $binaryData);

        return '/storage/' . $storageRelativePath;
    }

    /**
     * Parse baris header untuk mengambil Seksi & Bobot Soal.
     */
    protected function parseHeaderMetadata(string $headerText, array &$data): void
    {
        // Cari Seksi (moji_goi, bunpou, dokkai, choukai)
        if (preg_match('/SEKSI\s*:\s*([a-z_]+)/i', $headerText, $m)) {
            $sec = strtolower(trim($m[1]));
            if (in_array($sec, ['moji_goi', 'bunpou', 'dokkai', 'choukai'])) {
                $data['section_type'] = $sec;
            }
        } elseif (stripos($headerText, 'DOKKAI') !== false) {
            $data['section_type'] = 'dokkai';
        } elseif (stripos($headerText, 'CHOUKAI') !== false) {
            $data['section_type'] = 'choukai';
        } elseif (stripos($headerText, 'BUNPOU') !== false) {
            $data['section_type'] = 'bunpou';
        } elseif (stripos($headerText, 'MOJI') !== false || stripos($headerText, 'GOI') !== false) {
            $data['section_type'] = 'moji_goi';
        }

        // Cari Bobot Poin
        if (preg_match('/(?:BOBOT|POIN)\s*:\s*([0-9\.]+)/i', $headerText, $m)) {
            $data['score_points'] = (float) $m[1];
        }

        // Cari Indikator Capaian jika tertera di baris header
        if (preg_match('/INDIKATOR\s*:\s*([^\|]+)/i', $headerText, $m)) {
            $data['indicator'] = trim($m[1]);
        }
    }

    /**
     * Impor teks cepat berformat terstruktur (Aiken / Plain Text Format).
     */
    public function importFromAikenText(string $rawText, QuestionBank $package, ?int $userId = null): array
    {
        $userId = $userId ?? Auth::id();
        $blocks = preg_split('/\R{2,}/u', trim($rawText));
        $importedCount = 0;
        $errors = [];

        $currentMaxOrder = DB::table('question_bank_questions')
            ->where('question_bank_id', $package->id)
            ->max('order_index') ?? 0;

        foreach ($blocks as $idx => $block) {
            $num = $idx + 1;
            $lines = array_filter(array_map('trim', explode("\n", $block)));
            if (empty($lines)) {
                continue;
            }

            $questionText = '';
            $options = [];
            $answerKey = 'A';
            $explanation = null;

            foreach ($lines as $line) {
                if (preg_match('/^(?:ANSWER|JAWABAN|KUNCI)\s*:\s*([A-E])/i', $line, $m)) {
                    $answerKey = strtoupper($m[1]);
                } elseif (preg_match('/^(?:EXPLANATION|PEMBAHASAN|KAITSU)\s*:\s*(.+)$/i', $line, $m)) {
                    $explanation = trim($m[1]);
                } elseif (preg_match('/^([A-E])[\.\)]\s*(.+)$/i', $line, $m)) {
                    $options[] = [
                        'key' => strtoupper($m[1]),
                        'text' => trim($m[2]),
                    ];
                } else {
                    $questionText .= ($questionText === '' ? '' : ' ') . $line;
                }
            }

            // Bersihkan nomor soal di awal jika ada (misal: "1. Apa itu...")
            $questionText = preg_replace('/^\d+[\.\)]\s*/u', '', $questionText);

            if (empty($questionText)) {
                $errors[] = "Blok #{$num}: Teks pertanyaan tidak ditemukan.";
                continue;
            }

            if (count($options) < 2) {
                $errors[] = "Blok #{$num}: Pilihan jawaban kurang dari 2.";
                continue;
            }

            DB::transaction(function () use ($package, $userId, $questionText, $options, $answerKey, $explanation, &$currentMaxOrder) {
                $currentMaxOrder++;

                $question = Question::create([
                    'created_by' => $userId,
                    'level' => $package->level ?? 'N4',
                    'section_type' => 'moji_goi',
                    'question_text' => $questionText,
                    'score_points' => 1.0,
                    'explanation' => $explanation,
                ]);

                foreach ($options as $opt) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_key' => $opt['key'],
                        'option_text' => $opt['text'],
                        'is_correct' => ($opt['key'] === $answerKey),
                    ]);
                }

                DB::table('question_bank_questions')->insert([
                    'question_bank_id' => $package->id,
                    'question_id' => $question->id,
                    'section_type' => 'moji_goi',
                    'order_index' => $currentMaxOrder,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

            $importedCount++;
        }

        return [
            'success' => $importedCount > 0,
            'total_blocks' => count($blocks),
            'imported_count' => $importedCount,
            'failed_count' => count($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Sematkan gambar ke dalam cell tabel Word, dengan konversi otomatis SVG ke PNG jika diperlukan.
     */
    protected function addImageToCell(Cell $cell, string $localImagePath): void
    {
        try {
            $ext = strtolower(pathinfo($localImagePath, PATHINFO_EXTENSION));
            $targetPath = $localImagePath;

            // Jika gambar bertipe SVG, konversi ke PNG beresolusi tinggi menggunakan Imagick
            if ($ext === 'svg' && extension_loaded('imagick')) {
                $cacheDir = storage_path('app/svg_cache');
                if (!is_dir($cacheDir)) {
                    @mkdir($cacheDir, 0755, true);
                }
                $pngPath = $cacheDir . '/' . md5($localImagePath) . '.png';
                if (!file_exists($pngPath) || filemtime($localImagePath) > filemtime($pngPath)) {
                    $im = new \Imagick();
                    $im->readImage($localImagePath);
                    $im->setImageFormat('png');
                    $im->writeImage($pngPath);
                    $im->clear();
                    $im->destroy();
                }
                $targetPath = $pngPath;
                $ext = 'png';
            }

            $supportedExts = ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'tiff'];
            if (in_array($ext, $supportedExts)) {
                $cell->addImage($targetPath, [
                    'width' => 250,
                    'height' => 150,
                    'alignment' => Jc::CENTER,
                ]);
            } else {
                $cell->addText('[Lampiran Gambar: ' . basename($localImagePath) . ']', [
                    'italic' => true,
                    'color' => '64748B',
                    'size' => 9,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning("Gagal menyematkan gambar ke Word: " . $e->getMessage());
            $cell->addText('[Lampiran Gambar: ' . basename($localImagePath) . ']', [
                'italic' => true,
                'color' => '64748B',
                'size' => 9,
            ]);
        }
    }

    /**
     * Bersihkan teks dari tag ruby dan lakukan encoding karakter khusus XML untuk naskah Word.
     */
    protected function safeDocxText(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }
        return htmlspecialchars($this->cleanRubyText($text), ENT_NOQUOTES, 'UTF-8');
    }

    /**
     * Hapus tag ruby agar bersih saat diekspor ke plain text docx.
     */
    protected function cleanRubyText(?string $text): string
    {
        if (empty($text)) {
            return '';
        }
        // Bersihkan tag <rp> terlebih dahulu agar tanda kurung tidak ganda
        $text = preg_replace('/<rp>.*?<\/rp>/iu', '', $text);
        // Ganti <ruby>漢字<rt>かんじ</rt></ruby> menjadi 漢字(かんじ) untuk cetakan kertas
        $text = preg_replace('/<ruby[^>]*>(.*?)<rt[^>]*>(.*?)<\/rt><\/ruby>/iu', '$1($2)', $text);
        return strip_tags($text);
    }

    /**
     * Tambahkan teks multi-baris secara rapi ke dalam cell Word.
     */
    protected function addMultilineText(Cell $cell, string $text, array $fontStyle = [], array $paraStyle = ['spaceAfter' => 40]): void
    {
        $lines = explode("\n", $text);
        foreach ($lines as $line) {
            $cell->addText($line, $fontStyle, $paraStyle);
        }
    }
}
