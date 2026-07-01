<?php

namespace App\Services\AI;

use App\Models\AiDocumentChunk;
use Illuminate\Support\Str;

class KoperasiAIChatService
{
    private const STOP_WORDS = [
        'yang', 'dan', 'di', 'ke', 'dari', 'untuk', 'dengan', 'pada',
        'adalah', 'ini', 'itu', 'saya', 'anda', 'kami', 'mereka',
        'apa', 'bagaimana', 'kenapa', 'bila', 'mana', 'siapa', 'macam',
        'tentang', 'sahaja', 'sebagai', 'oleh', 'atau', 'tetapi', 'juga',
        'sudah', 'belum', 'lagi', 'boleh', 'akan', 'telah', 'sedang',
        'ada', 'tidak', 'bukan', 'tolong', 'nak', 'mahu', 'hendak', 'perlu',
        'tu', 'ni', 'je', 'lah', 'kah', 'lah', 'pun', 'ya', 'oh', 'ha',
        'dah', 'kan', 'i', 'the', 'a', 'an', 'is', 'it', 'to', 'in',
    ];

    private bool $useDemoData = false;

    private const DEMO_CHUNKS = [
        [
            'topic' => 'keahlian',
            'content' => 'Syarat keahlian Koperasi Demo Berhad adalah: warganegara Malaysia berumur 18 tahun ke atas, berpendapatan tetap, dan tidak muflis. Yuran pendaftaran ialah RM50 manakala yuran keahlian tahunan adalah RM10 sahaja.',
        ],
        [
            'topic' => 'keahlian',
            'content' => 'Pendaftaran keahlian boleh dibuat secara dalam talian melalui portal ahli atau secara manual di pejabat koperasi. Pemohon perlu mengisi borang keahlian dan melampirkan salinan kad pengenalan.',
        ],
        [
            'topic' => 'pembiayaan',
            'content' => 'Pembiayaan peribadi Koperasi Demo Berhad menawarkan pembiayaan sehingga RM50,000 dengan tempoh bayaran balik sehingga 5 tahun (60 bulan). Kadar keuntungan bermula dari 4% setahun atas baki berkurangan.',
        ],
        [
            'topic' => 'pembiayaan',
            'content' => 'Cara mohon pembiayaan: 1) Log masuk ke portal ahli. 2) Klik menu Permohonan Pembiayaan. 3) Pilih jenis pembiayaan. 4) Isi borang dan jumlah yang dimohon. 5) Muat naik dokumen sokongan. 6) Hantar permohonan.',
        ],
        [
            'topic' => 'pinjaman',
            'content' => 'Pinjaman perumahan koperasi tersedia untuk pembelian rumah pertama, rumah teres, kondominium, dan rumah kampung. Pembiayaan perumahan maksimum RM200,000 dengan tempoh bayaran balik sehingga 15 tahun.',
        ],
        [
            'topic' => 'pinjaman',
            'content' => 'Pinjaman kenderaan ditawarkan kepada ahli untuk membeli kereta dan motosikal baharu atau terpakai. Kadar keuntungan bermula 3.5% setahun dan tempoh bayaran balik sehingga 9 tahun untuk kereta baharu.',
        ],
        [
            'topic' => 'dokumen',
            'content' => 'Dokumen yang diperlukan untuk permohonan pembiayaan: 1) Salinan kad pengenalan. 2) Slip gaji 3 bulan terkini. 3) Penyata bank 3 bulan. 4) Bil utiliti terkini sebagai bukti alamat. 5) Dokumen sokongan lain jika berkaitan.',
        ],
        [
            'topic' => 'dividen',
            'content' => 'Dividen koperasi dibayar setiap tahun selepas Mesyuarat Agung Tahunan (AGM). Kadar dividen ditetapkan berdasarkan prestasi kewangan koperasi dan diluluskan oleh ahli dalam AGM. Dividen akan dikreditkan terus ke akaun simpanan ahli.',
        ],
        [
            'topic' => 'dividen',
            'content' => 'Agihan keuntungan koperasi merangkumi dividen kepada ahli, bonus berdasarkan urus niaga, dan rizab koperasi. Pembayaran dividen tahun lepas adalah sebanyak 6% daripada modal saham dan 2% bonus dividen.',
        ],
        [
            'topic' => 'simpanan',
            'content' => 'Simpanan wajib ahli koperasi adalah sebanyak RM20 sebulan yang akan dipotong terus daripada pendapatan atau dibayar secara manual di pejabat koperasi. Ahli juga boleh membuat simpanan sukarela pada bila-bila masa tanpa had minimum.',
        ],
        [
            'topic' => 'simpanan',
            'content' => 'Simpanan sukarela memberi pulangan dividen yang kompetitif. Ahli boleh membuat pengeluaran simpanan sukarela pada bila-bila masa dengan mengisi borang pengeluaran di pejabat koperasi atau melalui portal ahli.',
        ],
        [
            'topic' => 'kemaskini',
            'content' => 'Kemaskini profil ahli boleh dilakukan secara dalam talian melalui portal ahli di www.koperasidemo.my/ahli. Ahli perlu log masuk, klik pada menu Profil, dan kemaskini maklumat seperti nombor telefon, alamat, dan emel.',
        ],
        [
            'topic' => 'operasi',
            'content' => 'Waktu operasi pejabat Koperasi Demo Berhad adalah Isnin hingga Jumaat dari 8:00 pagi hingga 5:00 petang. Pejabat tutup pada hujung minggu dan cuti umum. Kaunter perkhidmatan dibuka sehingga 4:30 petang.',
        ],
        [
            'topic' => 'faedah',
            'content' => 'Faedah menjadi ahli koperasi: 1) Mendapat dividen tahunan. 2) Kelayakan memohon pembiayaan peribadi, perumahan dan kenderaan. 3) Perlindungan insurans kelompok. 4) Diskaun di kedai koperasi. 5) Peluang menyertai aktiviti keahlian.',
        ],
        [
            'topic' => 'insurans',
            'content' => 'Ahli Koperasi Demo Berhad dilindungi oleh insurans kelompok secara percuma. Perlindungan merangkumi kematian RM10,000 dan keilatan kekal RM10,000. Ahli yang memohon pembiayaan juga dilindungi insurans pembiayaan.',
        ],
        [
            'topic' => 'pertanyaan',
            'content' => 'Sebarang pertanyaan boleh dikemukakan melalui emel ke admin@koperasidemo.my, WhatsApp di 012-3456789, atau datang terus ke pejabat koperasi di tingkat 1, Wisma Koperasi, Jalan bahagia, 50050 Kuala Lumpur.',
        ],
    ];

    public function ask(string $question): string
    {
        $keywords = $this->extractKeywords($question);

        if (AiDocumentChunk::count() === 0) {
            $this->useDemoData = true;
        }

        $chunks = $this->searchChunks($keywords);

        if ($chunks->isEmpty()) {
            return 'Maaf, maklumat yang anda cari tidak dijumpai dalam dokumen koperasi. Sila cuba soalan lain atau hubungi pihak koperasi untuk bantuan lanjut.';
        }

        $source = $this->useDemoData ? 'demo' : 'dokumen';
        $response = "Berdasarkan {$source} koperasi:\n\n";
        foreach ($chunks as $i => $chunk) {
            $response .= ($i + 1) . '. ' . trim($chunk['content'] ?? $chunk->content) . "\n\n";
        }

        return trim($response);
    }

    private function extractKeywords(string $question): array
    {
        $words = preg_split('/[\s\p{P}]+/u', Str::lower($question));

        return array_values(array_filter($words, fn ($word) =>
            strlen($word) > 2 && !in_array($word, self::STOP_WORDS)
        ));
    }

    private function searchChunks(array $keywords)
    {
        if (empty($keywords)) {
            return collect();
        }

        if ($this->useDemoData) {
            return collect(self::DEMO_CHUNKS)->filter(function ($chunk) use ($keywords) {
                foreach ($keywords as $keyword) {
                    if (str_contains(Str::lower($chunk['content']), $keyword)) {
                        return true;
                    }
                }
                return false;
            })->take(10)->values();
        }

        $query = AiDocumentChunk::query();

        foreach ($keywords as $keyword) {
            $query->orWhere('content', 'LIKE', '%' . $keyword . '%');
        }

        return $query->limit(10)->get();
    }
}
