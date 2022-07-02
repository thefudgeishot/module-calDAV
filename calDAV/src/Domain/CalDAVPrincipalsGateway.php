<?php
namespace Gibbon\Module\calDAV\Domain; //Replace ModuleName with your module's name, ommiting spaces

use Gibbon\Domain\Traits\TableAware;
use Gibbon\Domain\QueryCriteria;
use Gibbon\Domain\QueryableGateway;

/**
 * Name Gateway
 *
 * @version v21
 * @since   v21
 */
class CalDAVPrincipalsGateway extends QueryableGateway 
{
    use TableAware;

    private static $tableName = 'principals'; //The name of the table you will primarily be querying
    private static $primaryKey = 'id'; //The primaryKey of said table
    private static $searchableColumns = []; // Optional: Array of Columns to be searched when using the search filter
    
}
