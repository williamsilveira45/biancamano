DROP PROCEDURE IF EXISTS SumarizarVencimento;
create
    definer = root@localhost procedure SumarizarVencimento(IN checksum_file varchar(40))
BEGIN
    INSERT INTO biancamano.vencimento_summaries (emissao_nota,
                                                  data_vencimento_original,
                                                  competencia,
                                                  natureza_financeira,
                                                  operacao,
                                                  valor,
                                                  data_entrada,
                                                  emissora_titulo,
                                                  titulo_pago,
                                                  file_checksum,
                                                 conta_id)
    SELECT DATE_FORMAT(trial_data.emissao_nota, '%Y-%m-01') AS emissao_nota,
           DATE_FORMAT(trial_data.data_vencimento_original, '%Y-%m-01') AS data_vencimento_original,
           DATE_FORMAT(trial_data.competencia, '%Y-%m-01') AS competencia,
           trial_data.natureza_financeira AS natureza_financeira,
           trial_data.operacao AS operacao,
           SUM(trial_data.valor_original),
           DATE_FORMAT(trial_data.data_entrada, '%Y-%m-01') AS data_entrada,
           trial_data.emissora_titulo,
           trial_data.titulo_pago,
           trial_data.file_checksum,
           trial_data.conta_id
    FROM trial_data
    WHERE trial_data.file_checksum COLLATE utf8mb4_general_ci = checksum_file
    GROUP BY trial_data.emissao_nota, trial_data.data_vencimento_original, trial_data.competencia, trial_data.natureza_financeira, trial_data.operacao, trial_data.data_entrada, trial_data.emissora_titulo, trial_data.titulo_pago, trial_data.file_checksum, trial_data.conta_id;
END;
