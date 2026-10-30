<?php
// CLI uniquement : aucun identifiant n'est écrit dans la sauvegarde SQL.
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
require dirname(__DIR__) . '/wp-load.php';
$directory = dirname(__DIR__) . '/.backups/' . date('Y-m-d_His');
if (!mkdir($directory, 0700, true)) { throw new RuntimeException('Dossier inaccessible'); }
$environment = getenv();
$environment['MYSQL_PWD'] = DB_PASSWORD;
$command = array('C:/wamp64/bin/mysql/mysql8.4.7/bin/mysqldump.exe', '--host=' . DB_HOST, '--user=' . DB_USER, '--single-transaction', '--no-tablespaces', '--hex-blob', '--result-file=' . $directory . '/database.sql', DB_NAME);
$process = proc_open($command, array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes, null, $environment);
fclose($pipes[0]);
$output = stream_get_contents($pipes[1]); fclose($pipes[1]);
$error = stream_get_contents($pipes[2]); fclose($pipes[2]);
if (proc_close($process) !== 0) { throw new RuntimeException('Export SQL échoué : ' . $error); }
$zip = new ZipArchive();
if ($zip->open($directory . '/site-files.zip', ZipArchive::CREATE) !== true) { throw new RuntimeException('Archive inaccessible'); }
$root = dirname(__DIR__);
$iterator = new RecursiveIteratorIterator(new RecursiveCallbackFilterIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS), function ($file) {
    return !in_array($file->getFilename(), array('.backups', '.git'), true);
}));
foreach ($iterator as $file) {
    if ($file->isFile()) { $zip->addFile($file->getPathname(), str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1))); }
}
$zip->close();
$check = new ZipArchive();
if ($check->open($directory . '/site-files.zip', ZipArchive::CHECKCONS) !== true) { throw new RuntimeException('Archive invalide'); }
$count = $check->numFiles; $check->close();
$manifest = array('created_at' => gmdate('c'), 'files' => $count, 'database_sha256' => hash_file('sha256', $directory . '/database.sql'), 'archive_sha256' => hash_file('sha256', $directory . '/site-files.zip'), 'restoration_tested' => false);
file_put_contents($directory . '/manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));
echo $directory . PHP_EOL . $count . " fichiers archivés ; export SQL terminé.\n";
