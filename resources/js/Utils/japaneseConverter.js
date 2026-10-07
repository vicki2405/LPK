/**
 * Algoritma Konversi Romaji ke Hiragana & Katakana
 * Mendukung Hepburn Romaji, konsonan ganda (sokuon っ), kombo (youon ゃ/ゅ/ょ), dan tanda baca Jepang.
 */

const ROMAJI_TO_HIRAGANA_MAP = {
    // 3-huruf combos (Youon & khusus)
    'kya': 'きゃ', 'kyu': 'きゅ', 'kyo': 'きょ',
    'sha': 'しゃ', 'shu': 'しゅ', 'sho': 'しょ',
    'cha': 'ちゃ', 'chu': 'ちゅ', 'cho': 'ちょ',
    'nya': 'にゃ', 'nyu': 'にゅ', 'nyo': 'にょ',
    'hya': 'ひゃ', 'hyu': 'ひゅ', 'hyo': 'ひょ',
    'mya': 'みゃ', 'myu': 'みゅ', 'myo': 'みょ',
    'rya': 'りゃ', 'ryu': 'りゅ', 'ryo': 'りょ',
    'gya': 'ぎゃ', 'gyu': 'ぎゅ', 'gyo': 'ぎょ',
    'bya': 'びゃ', 'byu': 'びゅ', 'byo': 'びょ',
    'pya': 'ぴゃ', 'pyu': 'ぴゅ', 'pyo': 'ぴょ',
    'shi': 'し', 'chi': 'ち', 'tsu': 'つ', 'dzu': 'づ',
    'ji': 'じ', 'zi': 'じ', 'ja': 'じゃ', 'ju': 'じゅ', 'jo': 'じょ',
    'fa': 'ふぁ', 'fi': 'ふぃ', 'fe': 'ふぇ', 'fo': 'ふぉ',
    'ti': 'てぃ', 'di': 'でぃ', 'tu': 'とぅ', 'du': 'どぅ',

    // 2-huruf
    'ka': 'か', 'ki': 'き', 'ku': 'く', 'ke': 'け', 'ko': 'こ',
    'sa': 'さ', 'su': 'す', 'se': 'せ', 'so': 'そ',
    'ta': 'た', 'te': 'て', 'to': 'と',
    'na': 'な', 'ni': 'に', 'nu': 'ぬ', 'ne': 'ね', 'no': 'の',
    'ha': 'は', 'hi': 'ひ', 'fu': 'ふ', 'hu': 'ふ', 'he': 'へ', 'ho': 'ほ',
    'ma': 'ま', 'mi': 'み', 'mu': 'む', 'me': 'め', 'mo': 'も',
    'ya': 'や', 'yu': 'ゆ', 'yo': 'よ',
    'ra': 'ら', 'ri': 'り', 'ru': 'る', 're': 'れ', 'ro': 'ろ',
    'wa': 'わ', 'wo': 'を',
    'ga': 'が', 'gi': 'ぎ', 'gu': 'ぐ', 'ge': 'げ', 'go': 'ご',
    'za': 'ざ', 'zu': 'ず', 'ze': 'ぜ', 'zo': 'ぞ',
    'da': 'だ', 'de': 'で', 'do': 'ど',
    'ba': 'ば', 'bi': 'び', 'bu': 'ぶ', 'be': 'べ', 'bo': 'ぼ',
    'pa': 'ぱ', 'pi': 'ぴ', 'pu': 'ぷ', 'pe': 'ぺ', 'po': 'ぽ',
    'nn': 'ん', "n'": 'ん',

    // 1-huruf
    'a': 'あ', 'i': 'い', 'u': 'う', 'e': 'え', 'o': 'お',

    // Simbol & Tanda Baca
    '-': 'ー',
    ',': '、',
    '.': '。',
    '[': '「',
    ']': '」',
    '?': '？',
    '!': '！',
};

// Katakana Map based on Hiragana
const HIRAGANA_START = 0x3041;
const HIRAGANA_END = 0x3096;
const KATAKANA_OFFSET = 0x60;

export function hiraganaToKatakana(hiraganaText) {
    return hiraganaText.replace(/[\u3041-\u3096]/g, (char) => {
        return String.fromCharCode(char.charCodeAt(0) + KATAKANA_OFFSET);
    });
}

/**
 * Mengonversi teks Romaji menjadi Hiragana secara real-time.
 */
export function romajiToHiragana(text) {
    if (!text) return '';

    let result = '';
    let i = 0;
    const len = text.length;

    while (i < len) {
        // Cek 3 karakter
        const three = text.substring(i, i + 3).toLowerCase();
        if (ROMAJI_TO_HIRAGANA_MAP[three]) {
            result += ROMAJI_TO_HIRAGANA_MAP[three];
            i += 3;
            continue;
        }

        // Cek konsonan ganda sokuon (contoh: kk -> っk, tt -> っt, ss -> っs)
        if (i + 1 < len) {
            const c1 = text[i].toLowerCase();
            const c2 = text[i + 1].toLowerCase();
            if (c1 === c2 && c1 !== 'n' && c1 !== 'a' && c1 !== 'i' && c1 !== 'u' && c1 !== 'e' && c1 !== 'o' && /[a-z]/.test(c1)) {
                result += 'っ';
                i += 1;
                continue;
            }
        }

        // Cek 2 karakter
        const two = text.substring(i, i + 2).toLowerCase();
        if (ROMAJI_TO_HIRAGANA_MAP[two]) {
            result += ROMAJI_TO_HIRAGANA_MAP[two];
            i += 2;
            continue;
        }

        // Khusus huruf 'n' sebelum spasi atau akhir kalimat atau konsonan selain y/w
        if (text[i].toLowerCase() === 'n') {
            const nextChar = text[i + 1]?.toLowerCase();
            if (!nextChar || (nextChar !== 'a' && nextChar !== 'i' && nextChar !== 'u' && nextChar !== 'e' && nextChar !== 'o' && nextChar !== 'y')) {
                result += 'ん';
                i += 1;
                continue;
            }
        }

        // Cek 1 karakter
        const one = text[i].toLowerCase();
        if (ROMAJI_TO_HIRAGANA_MAP[one]) {
            result += ROMAJI_TO_HIRAGANA_MAP[one];
            i += 1;
            continue;
        }

        // Simpan karakter asli (spasi, huruf lainnya)
        result += text[i];
        i += 1;
    }

    return result;
}

/**
 * Format Furigana Tag Ruby
 */
export function createFuriganaRuby(kanji, reading) {
    if (!kanji || !reading) return kanji || reading || '';
    return `<ruby>${kanji}<rt>${reading}</rt></ruby>`;
}
