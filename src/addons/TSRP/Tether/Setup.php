<?php

namespace TSRP\Tether;

use XF\AddOn\AbstractSetup;

class Setup extends AbstractSetup
{
    public function install(array $stepParams = []): void
    {
        $this->installStep1();
    }

    public function installStep1(): void
    {
        $data = [
            'tag' => 'tether',
            'type' => 'callback',
            'callback_class' => 'TSRP\\Tether\\BbCode\\Tether',
            'callback_method' => 'render',
            'example' => '[tether=arachnas-swansong]positive1,negative5[/tether]',
            'description' => 'Render tether images with a popup that shows tether details.',
            'active' => 1,
            'trim' => 1,
            'allow_signature' => 1,
            'addon_id' => 'TSRP/Tether'
        ];

        $this->insertBbCode('xf_bb_code', $data);
    }

    public function uninstallStep1(): void
    {
        $column = $this->getBbCodeTagColumn('xf_bb_code');
        $this->db()->delete('xf_bb_code', "{$column} = ?", 'tether');
    }

    public function upgrade(array $stepParams = []): void
    {
        // No upgrade steps yet.
    }

    public function uninstall(array $stepParams = []): void
    {
        $this->uninstallStep1();
    }

    protected function insertBbCode(string $table, array $data): void
    {
        $columns = $this->getTableColumns($table);
        $insert = [];

        foreach ($data as $column => $value)
        {
            $dbColumn = $this->resolveBbCodeColumn($columns, $column);
            if ($dbColumn)
            {
                $insert[$dbColumn] = $value;
            }
        }

        if ($insert)
        {
            $this->db()->insert($table, $insert);
        }
    }

    protected function getBbCodeTagColumn(string $table): string
    {
        $columns = $this->getTableColumns($table);
        return isset($columns['bb_code_tag']) ? 'bb_code_tag' : 'tag';
    }

    protected function resolveBbCodeColumn(array $columns, string $column): ?string
    {
        if (isset($columns[$column]))
        {
            return $column;
        }

        $legacyColumn = "bb_code_{$column}";
        if (isset($columns[$legacyColumn]))
        {
            return $legacyColumn;
        }

        return null;
    }

    protected function getTableColumns(string $table): array
    {
        return $this->db()->getSchemaManager()->getTableColumns($table);
    }
}
