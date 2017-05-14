# stinky-query-finder
Find what will probably turn out to be totally awful MySQL queries before they ruin everything.
[![Build Status](https://travis-ci.org/danieljharvey/stinky-query-finder.svg?branch=master)](https://travis-ci.org/danieljharvey/stinky-query-finder)

<b>What does it do then?</b>

You pass it a PDO object and a SQL call, it learns a little about your DB, and works out whether your query is potentially garbage

<b>How does it decide?</b>

Basically, by whether
a) the table is very long - if it's not, it'll be fine.
b) if the table IS long, whether the query is using one or more indexes to give it a fighting chance of being OK.

<b>How do I use it?</b>

Make a PDO object, pass it into a nice new Stinkers object, and ask it whether your SQL is dogshit or not.

~~~~
$dbName = "greatDB"

$dsn = "mysql:dbname={$dbName};host=127.0.0.1";

$pdo = new \PDO($dsn, "username", "excellentPassword");

$sql = "SELECT COUNT(1) FROM excellentTable WHERE thingID=2229 AND theDate='2017-05-12'";

$stinkers = new \DanielJHarvey\QueryStinkers\Stinkers($dbName, $tables, $pdo);

$problematicQuery = $stinkers->checkQuery($sql);
~~~~

$problematicQuery will either return false (not a problem, great) or an array-based stack trace (so that the offending query creating code can be located)

<b>That's going to slow things down a bit isn't it?</b>

Yeah, unfortunately so, so please please please don't use this in production. If you wish to speed things up, you can cache the DB tables data created by Stinkers like this:

~~~~

$dbName="excellentDB"
$tables = getCachedTablesFromYourGreatCachingFunction();

$stinkers = new \DanielJHarvey\QueryStinkers\Stinkers($dbName, $tables);

// no need to rebuild table data, everything is fine

~~~~
