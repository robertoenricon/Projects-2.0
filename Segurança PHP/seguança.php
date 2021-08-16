<?php
echo '<pre>';

/*

* Configurações php.ini

*/

/*
  allow_url_fopen & allow_url_include = off 
  
  O que pode causar se habilitado? 
  Vulnerável a ataques de:
  - inclusão remotas de arquivos (RFI). Possibilidade de inserção de um arquivo podem acessar o phpinfo() e roubar informações
  - negação de serviços (DoS). Pode fazer com que sua aplicação fique off-line
  
  Obs: Caso precise dessa opção habilitada para compartilhamentos de arquivos. Usar o REST
 */
print_r([
  "allow_url_fopen" => ini_get('allow_url_fopen'),
  "allow_url_include" => ini_get('allow_url_include')
]);


/*
 * [ DoS (Denial of Service) ] Configurações que melhoram performance e ajudam a evitar
 * ataques de negação de serviço.
 *
 * [ memory_limit ] 64m (pequenas), 128m (a maioria) e 512m ou + (grandes) de memória alocada
 * para o PHP. Uma regra básica de 1/4 (da memória do servidor) pode ser aplicada.
 *
 * [ max_execution_time ] O padrão é 30s, mas vamos buscar o mínimo possível ou o máximo
 * aceitável (5s) para seu usuário esperar. 
 
 * 5s no máximo de max_execution_time, previni ataque DoS. Se o servidor ficar um pouco lento, ele não vai processar depois de 5s. 
 * Caso tenha rotinas como (CRON) que precise de mais tempo para executar, criar uma camada separada.
 *
 * [ max_input_time ] É o tempo que o PHP interpreta dados vindos via GET ou POST. O padrão
 * é 60 segundos, e esse é um bom número devido ao tratamento da aplicação.
 */

var_dump([
  "memory_get_peak_usage" => $mem = memory_get_peak_usage(),
  "memory_get_peak_usage | M" => number_format($mem / (1024 * 1024), 2) . "M",
], [
  "memory_limit" => ini_get("memory_limit"),
  "max_execution_time" => ini_get("max_execution_time"),
  "max_input_time" => ini_get("max_input_time")
]);


/*
 * [ post_max_size ] Limite máximo permitido em dados vindos de um formulário.

 * [ max_input_nesting_level ] Profundidade máxima permitida em um vetor. (GET, POST)
 *
 * [ file_uploads ] Permiter ou não o envio de arquivos em formulários.
 *
 * [ upload_max_filesize ] Tamanho máximo de UM arquivo no formulário.
 *
 * [ max_file_uploads ] Quantidade máxima de arquivos em UMA requisição
 */

var_dump([
  "post_max_size" => ini_get("post_max_size"),
  "max_input_nesting_level" => ini_get("max_input_nesting_level"),
  "file_uploads" => ini_get("file_uploads"),
  "upload_max_filesize" => ini_get("upload_max_filesize"),
  "max_file_uploads" => ini_get("max_file_uploads") //padrão 20
]);


/*
 * [ output_buffering ] Limita a quantidade de requisições melhorando a performance da
 * aplicação ao empurrar todos os comandos de saída para o final da requisição.
 *
 * [ implicit_flush ] Em off para empurrar o buffering para o final da saída. Em on
 * para descarregar a cada echo, print, etc.
 * 
 * Se output_buffering estiver ligado. Vai executar todos os comandos de saida (ex: echo), ele vai criar um cache 
 * e mandar tudo no final do script. Limitando requisições, vc tem uma aceleração no codigo. implicit_flush precisa estar desligado 
 */

var_dump([
  "output_buffering" => ini_get("output_buffering"), //recomendado 4096
  "implicit_flush" => ini_get("implicit_flush")
]);


/*
 * [ realpath_cache_size ] O PHP consegue manter um cache de arquivos usados em sua
 * aplicação para evitar reprocessamento e com isso melhora a performance.
 *
 * [ realpath_cache_ttl ] É o tempo em segundos deste cache.
 */

var_dump([
  "realpath_cache_size()" => realpath_cache_size(), //o valor é em Kbytes. 
  // Rodar ao final de cada arquivo para saber o valor retornado. Se o valor retornado for maior do que o (realpath_cache_size), 
  // necessario aumentar o realpath_cache_size para que sua aplicação tenha um tempo de processamento adequado no cache
], [
  "realpath_cache_size" => ini_get("realpath_cache_size"), //Padrao superior ao que a sua aplicação utiliza
  "realpath_cache_ttl" => ini_get("realpath_cache_ttl") // Padrao 120
]);


/*
 * [ OPcache ] Um cache bytecode de scripts PHP pré-compilados em memória compartilhada
 * que elimina a necessidade do PHP carregar e analisar scripts em cada requisição.
 * 
 *  - Procurar zend_extension=apache.so
 *  - Habilitar logo abaixo o ;opache.validate_timestamps=0
 *  
 * Definindo como zero(0), toda requisição criada, ao acesar o arquivo não vai ficar fazendo a leitura (verificar o arquivo), 
 * vai ser levado em consideração o cache armazenado. Isso é melhora muito o processamento em ambientes de Produção.
 * 
 * Como utilizar:
 *  - Ao habilitar o desabilitar é necessario o restart do apache
 *  - Para resetar essa opção utilizada, é necessario criar um arquivo novo dentro do projeto e dentro desse arquivo utilizar a função
 *  opache_reset(); para resetar o cache e assim será feita a nova leitura do arquivo.
 */

var_dump(
  opcache_get_configuration(),
  opcache_get_status()["scripts"] //ao habilitar o ;opache.validate_timestamps=0, ultima linha ('timestamp'), é removida do codigo,
  //pois não vai ter mais a leitura do arquivo, assim não sendo necessario gravar o ultimo horario executado.
);
