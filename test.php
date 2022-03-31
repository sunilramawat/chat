<?php

function getPHPVersion(): string
{
    return "v".PHP_MAJOR_VERSION . "." . PHP_MINOR_VERSION;
}

function getCryptoVersion(): string
{
    return file_get_contents(__DIR__ . DIRECTORY_SEPARATOR."..". DIRECTORY_SEPARATOR."VERSION");
}

function getExtensionsNamesAsArray(): array
{
    $extensions = [
        "vscf_foundation_php",
        "vsce_phe_php",
        "vscp_pythia_php"
    ];

    return $extensions;
}

function getScannedIniDir()
{
    $res = null;
    $rawData = php_ini_scanned_files();

    if ($rawData)
        $res = explode(",", $rawData);

    return pathinfo($res[0], PATHINFO_DIRNAME);
}

function getExtensionsInfo(): array
{
    $extArr = [];

    foreach (getExtensionsNamesAsArray() as $ext) {
        $extArr[] = [
            'name' => $ext,
            'version' => phpversion($ext) ?: null,
            'is_extension_loaded' => extension_loaded($ext),
        ];
    }

    return $extArr;
}

function getConfigInfo(): array
{
    return [
        'CRYPTO_VERSION' => getCryptoVersion(),
        'OS' => PHP_OS,
        'PHP_VERSION' => getPHPVersion(),
        'PATH_TO_EXTENSIONS_DIR' => PHP_EXTENSION_DIR,
        'PATH_TO_MAIN_PHP.INI' => php_ini_loaded_file(),
        'PATH_TO_ADDITIONAL_INI_FILES' => getScannedIniDir(),
    ];
}

echo "<pre>".var_dump(getExtensionsInfo(), getConfigInfo())."</pre>";
exit(0);