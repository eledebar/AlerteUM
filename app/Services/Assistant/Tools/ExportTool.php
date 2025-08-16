<?php
namespace App\Services\Assistant\Tools;

class ExportTool
{
    public function urlCsvUtilisateur(): string
    {
        return route('utilisateur.incidents.export.csv');
    }
}
