<?php
namespace Concrete\Core\File\Search\ColumnSet;

use Concrete\Core\File\Search\ColumnSet\Column\FileVersionFilename;
use Concrete\Core\Search\Column\Column;

class Available extends DefaultSet
{
    protected $attributeClass = 'FileAttributeKey';

    public function __construct()
    {
        parent::__construct();
        $this->addColumn(new Column('fvAuthorName', t('Author'), 'getAuthorName', false));
        $this->addColumn(new FileVersionFilename());
    }
}
