<?php
/**
 * Tabelas oficiais CNJ + URLs de consulta processual
 * Base: Resolução CNJ nº 65/2008  (atualizado: mai / 2024)
 *
 * - 'base_cnj'        → host da Busca Processual Unificada
 * - 'orgaos'          → dígito “J”
 * - 'tribunais'       → par   “TR”
 * - 'urls_tribunais'  → link direto p/ consulta de cada tribunal
 *                       (valor termina em "="; o nº do processo será concatenado)
 */

return [

    /* ───────────────────────── DOMÍNIO CNJ ───────────────────────── */
    // se o CNJ trocar de host, troque APENAS esta linha
    'base_cnj' => 'https://www.cnj.jus.br/busca-ativa/processual/',

    /* ───────────────────────── URLs nativas ───────────────────────── */
    'urls_tribunais' => [

        /* ─────── TJ ESTADUAIS (ESAJ / e‑SAJ / Projudi) ─────── */
        'TJDFT' => 'https://pje.tjdft.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TJAC'  => 'https://esaj.tjac.jus.br/cpopg/open.do?numeroProcesso=',
        'TJAL'  => 'https://esaj.tjal.jus.br/cpopg/open.do?numeroProcesso=',
        'TJAM'  => 'https://esaj.tjam.jus.br/cpopg/open.do?numeroProcesso=',
        'TJAP'  => 'https://consulta.tjap.jus.br/consulta/numero?numero=',
        'TJBA'  => 'https://esaj.tjba.jus.br/cpopg/open.do?numeroProcesso=',
        'TJCE'  => 'https://esaj.tjce.jus.br/cpopg/open.do?numeroProcesso=',
        'TJES'  => 'https://esaj.tjes.jus.br/cpopg/open.do?numeroProcesso=',
        'TJGO'  => 'https://projudi.tjgo.jus.br/consultapublica/NumeroProcesso?numero=',
        'TJMA'  => 'https://pje.tjma.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TJMT'  => 'https://pje.tjmt.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TJMS'  => 'https://esaj.tjms.jus.br/cpopg/open.do?numeroProcesso=',
        'TJMG'  => 'https://consulta.tjmg.jus.br/processos/numero/?processo=',
        'TJPA'  => 'https://consultas.tjpa.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TJPB'  => 'https://pje.tjpb.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TJPR'  => 'https://projudi.tjpr.jus.br/projudi/processo/consultaNumeroProcesso.do?numero=',
        'TJPE'  => 'https://pje.tjpe.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TJPI'  => 'https://pje.tjpi.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TJRJ'  => 'https://pje.tjrj.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TJRN'  => 'https://pje.tjrn.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TJRS'  => 'https://esaj.tjrs.jus.br/cpopg/open.do?numeroProcesso=',
        'TJRO'  => 'https://pje.tjro.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TJRR'  => 'https://pje.tjrr.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TJSC'  => 'https://esaj.tjsc.jus.br/cpopg/open.do?numeroProcesso=',
        'TJSE'  => 'https://esaj.tjse.jus.br/cpopg/open.do?numeroProcesso=',
        'TJSP'  => 'https://esaj.tjsp.jus.br/cpopg/open.do?numeroProcesso=',
        'TJTO'  => 'https://pje.tjto.jus.br/consultaprocessual/NumeroProcesso?numero=',

        /* ─────────── TRF – Justiça Federal (PJe) ─────────── */
        'TRF‑1ª Região' => 'https://processual.trf1.jus.br/consultaProcessual/numeroProcesso.php?proc=',
        'TRF‑2ª Região' => 'https://pje.trf2.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TRF‑3ª Região' => 'https://web.trf3.jus.br/consultaprocessual/numeroProcesso?proc=',
        'TRF‑4ª Região' => 'https://processual.trf4.jus.br/processos/numero.php?numero=',
        'TRF‑5ª Região' => 'https://pje.trf5.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TRF‑6ª Região' => 'https://processual.trf6.jus.br/consultaprocessual/numeroProcesso?numero=',

        /* ─────────── TRT – Justiça do Trabalho (PJe) ─────────── */
        'TRT‑1ª Região'  => 'https://consultaprocessual1.trt1.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TRT‑2ª Região'  => 'https://pje.trt2.jus.br/consultaprocessual/NumeroProcessoSeparaNumeros?numero=',
        'TRT‑3ª Região'  => 'https://pje.trt3.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TRT‑4ª Região'  => 'https://pje.trt4.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TRT‑5ª Região'  => 'https://pje.trt5.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TRT‑6ª Região'  => 'https://pje.trt6.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TRT‑7ª Região'  => 'https://pje.trt7.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TRT‑8ª Região'  => 'https://pje.trt8.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TRT‑9ª Região'  => 'https://pje1g.trt9.jus.br/consultaprocessual/NumeroProcessoSeparaNumeros?numero=',
        'TRT‑10ª Região' => 'https://pje.trt10.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TRT‑11ª Região' => 'https://pje.trt11.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TRT‑12ª Região' => 'https://pje.trt12.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TRT‑13ª Região' => 'https://pje.trt13.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TRT‑14ª Região' => 'https://pje.trt14.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TRT‑15ª Região' => 'https://pje.trt15.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TRT‑16ª Região' => 'https://pje.trt16.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TRT‑17ª Região' => 'https://pje.trt17.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TRT‑18ª Região' => 'https://pje.trt18.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TRT‑19ª Região' => 'https://pje.trt19.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TRT‑20ª Região' => 'https://pje.trt20.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TRT‑21ª Região' => 'https://pje.trt21.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TRT‑22ª Região' => 'https://pje.trt22.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TRT‑23ª Região' => 'https://pje.trt23.jus.br/consultaprocessual/NumeroProcesso?numero=',
        'TRT‑24ª Região' => 'https://pje.trt24.jus.br/consultaprocessual/NumeroProcesso?numero=',
    ],

    /* ───────────────────────── ÓRGÃOS (J) ───────────────────────── */
    'orgaos' => [
        '1' => 'Supremo Tribunal Federal (STF)',
        '2' => 'Superior Tribunal de Justiça (STJ)',
        '3' => 'Justiça Federal',
        '4' => 'Justiça Estadual',
        '5' => 'Justiça do Trabalho',
        '6' => 'Justiça Eleitoral',
        '7' => 'Justiça Militar',
        '8' => 'Turmas Recursais / Juizados Especiais',
        '9' => 'Conselho Nacional de Justiça',
    ],

    /* ───────────── TRIBUNAIS / ÓRGÃOS JULGADORES (TR) ───────────── */
    'tribunais' => [

        /* TJs (01‑27) */
        '01' => 'TJDFT', '02' => 'TJAC', '03' => 'TJAL', '04' => 'TJAM',
        '05' => 'TJAP',  '06' => 'TJBA', '07' => 'TJCE', '08' => 'TJES',
        '09' => 'TJGO',  '10' => 'TJMA', '11' => 'TJMT', '12' => 'TJMS',
        '13' => 'TJMG',  '14' => 'TJPA', '15' => 'TJPB', '16' => 'TJPR',
        '17' => 'TJPE',  '18' => 'TJPI', '19' => 'TJRJ', '20' => 'TJRN',
        '21' => 'TJRS',  '22' => 'TJRO', '23' => 'TJRR', '24' => 'TJSC',
        '25' => 'TJSE',  '26' => 'TJSP', '27' => 'TJTO',

        /* TRE (30‑57) – Justiça Eleitoral */
        '30' => 'TRE‑AC', '31' => 'TRE‑AL', '32' => 'TRE‑AM', '33' => 'TRE‑AP',
        '34' => 'TRE‑BA', '35' => 'TRE‑CE', '36' => 'TRE‑DF', '37' => 'TRE‑ES',
        '38' => 'TRE‑GO', '39' => 'TRE‑MA', '40' => 'TRE‑MT', '41' => 'TRE‑MS',
        '42' => 'TRE‑MG', '43' => 'TRE‑PA', '44' => 'TRE‑PB', '45' => 'TRE‑PR',
        '46' => 'TRE‑PE', '47' => 'TRE‑PI', '48' => 'TRE‑RJ', '49' => 'TRE‑RN',
        '50' => 'TRE‑RS', '51' => 'TRE‑RO', '52' => 'TRE‑RR', '53' => 'TRE‑SC',
        '54' => 'TRE‑SE', '55' => 'TRE‑SP', '56' => 'TRE‑TO', '57' => 'TRE‑Exterior',

        /* TRF (60‑65) */
        '60' => 'TRF‑1ª Região', '61' => 'TRF‑2ª Região', '62' => 'TRF‑3ª Região',
        '63' => 'TRF‑4ª Região', '64' => 'TRF‑5ª Região', '65' => 'TRF‑6ª Região',

        /* STM – Justiça Militar da União (80) */
        '80' => 'STM',

        /* TRT (90‑113) */
        '90'  => 'TRT‑1ª Região',  '91'  => 'TRT‑2ª Região',  '92'  => 'TRT‑3ª Região',
        '93'  => 'TRT‑4ª Região',  '94'  => 'TRT‑5ª Região',  '95'  => 'TRT‑6ª Região',
        '96'  => 'TRT‑7ª Região',  '97'  => 'TRT‑8ª Região',  '98'  => 'TRT‑9ª Região',
        '99'  => 'TRT‑10ª Região', '100' => 'TRT‑11ª Região', '101' => 'TRT‑12ª Região',
        '102' => 'TRT‑13ª Região', '103' => 'TRT‑14ª Região', '104' => 'TRT‑15ª Região',
        '105' => 'TRT‑16ª Região', '106' => 'TRT‑17ª Região', '107' => 'TRT‑18ª Região',
        '108' => 'TRT‑19ª Região', '109' => 'TRT‑20ª Região', '110' => 'TRT‑21ª Região',
        '111' => 'TRT‑22ª Região', '112' => 'TRT‑23ª Região', '113' => 'TRT‑24ª Região',
    ],
];
