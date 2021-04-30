<?php 

require_once __DIR__ . '/vendor/autoload.php';
use Iodev\Whois\Factory;


$whois = Factory::get()->createWhois();
$info = $whois->loadDomainInfo("edinaldoribeiro.com.br");

echo "<pre>";
print_r([
    'Proprietário: ' => $info->owner,
    'Status: ' => $info->states[array_key_last($info->states)],
    'Domínio criado em: ' => date("d-m-Y", $info->creationDate),
    'Domínio expira em: ' => date("d-m-Y", $info->expirationDate),
]);

echo "<pre>";
echo $whois;