<?php
namespace Mongo\Stat;

class Stat extends \Io\Db\Mongo\Collection {

    protected $_connection_key = 'MONGO_STAT';
    protected $_db_name = 'stat';

}