# randomstate/camelot-php

A PHP wrapper for Camelot, the python PDF table extraction library

## Installation

`composer require randomstate/camelot-php`

## Usage

The package adheres closely with the camelot CLI API Usage.
Default output is in CSV format as a simple string. If you need to parse CSV strings we recommend the `league/csv` package (https://csv.thephpleague.com/)

```php
<?php

use RandomState\Camelot\Camelot;
use League\Csv\Reader;

$tables = Camelot::lattice('/path/to/my/file.pdf')
       ->extract();

$csv = Reader::createFromString($tables[0]);
$allRecords = $csv->getRecords();
```

### Advanced Processing

##### Saving / Extracting
**Note: No Camelot operations are run until one of these methods is run**
```php
$camelot->extract(); // uses temporary files and automatically grabs the table contents for you from each
$camelot->save('/path/to/my-file.csv'); // mirrors the behaviour of Camelot and saves files in the format /path/to/my-file-page-*-table-*.csv
$camelot->plot(); // useful for debugging, it will plot it in a separate window (see Visual Debugging below)   
```

##### [Set Format](https://camelot-py.readthedocs.io/en/master/user/quickstart.html#read-the-pdf)
```
$camelot->json();
$camelot->csv();
$camelot->html();
$camelot->excel();
$camelot->sqlite();
```
##### [Specify Page Numbers](https://camelot-py.readthedocs.io/en/master/user/quickstart.html#specify-page-numbers)

`$camelot->pages('1,2,3-4,8-end')`

##### [Reading encrypted PDFs](https://camelot-py.readthedocs.io/en/master/user/quickstart.html#reading-encrypted-pdfs)

`$camelot->password('my-pass')`

##### [Processing background lines](https://camelot-py.readthedocs.io/en/master/user/advanced.html#process-background-lines)
`$camelot->stream()->processBackgroundLines()`

##### [Visual debugging](https://camelot-py.readthedocs.io/en/master/user/advanced.html#visual-debugging)

`$camelot->plot()`

##### [Specify table areas](https://camelot-py.readthedocs.io/en/master/user/advanced.html#specify-table-areas)

```php
<?php

use RandomState\Camelot\Camelot;
use RandomState\Camelot\Areas;

Camelot::stream('my-file.pdf')
    ->inAreas(
        Areas::from($xTopLeft, $yTopLeft, $xBottomRight, $yBottomRight)
            // ->add($xTopLeft2, $yTopLeft2, $xBottomRight2, $yBottomRight2)
            // ->add($xTopLeft3, $yTopLeft3, $xBottomRight3, $yBottomRight3)
    );
```

##### [Specify table regions](https://camelot-py.readthedocs.io/en/master/user/advanced.html#specify-table-regions)

```php
<?php

use RandomState\Camelot\Camelot;
use RandomState\Camelot\Areas;

Camelot::stream('my-file.pdf')
    ->inRegions(
        Areas::from($xTopLeft, $yTopLeft, $xBottomRight, $yBottomRight)
            // ->add($xTopLeft2, $yTopLeft2, $xBottomRight2, $yBottomRight2)
            // ->add($xTopLeft3, $yTopLeft3, $xBottomRight3, $yBottomRight3)
    );
```
 
##### [Specify column separators](https://camelot-py.readthedocs.io/en/master/user/advanced.html#specify-column-separators)

`$camelot->stream()->setColumnSeparators($x1,$x2...)`

##### [Split text along separators](https://camelot-py.readthedocs.io/en/master/user/advanced.html#split-text-along-separators)

`$camelot->split()`

##### [Flag superscripts and subscripts](https://camelot-py.readthedocs.io/en/master/user/advanced.html#flag-superscripts-and-subscripts)

`$camelot->flagSize()`

##### [Strip characters from text](https://camelot-py.readthedocs.io/en/master/user/advanced.html#strip-characters-from-text)

`$camelot->strip("\n")`

##### [Improve guessed table areas](https://camelot-py.readthedocs.io/en/master/user/advanced.html#improve-guessed-table-areas)

`$camelot->setEdgeTolerance(500)`

##### [Improve guessed table rows](https://camelot-py.readthedocs.io/en/master/user/advanced.html#improve-guessed-table-rows)

`$camelot->setRowTolerance(15)`

##### [Detect short lines](https://camelot-py.readthedocs.io/en/master/user/advanced.html#detect-short-lines)

`$camelot->lineScale(20)`


##### [Shift text in spanning cells](https://camelot-py.readthedocs.io/en/master/user/advanced.html#shift-text-in-spanning-cells)

`$camelot->shiftText('r', 'b')`

##### [Copy text in spanning cells](https://camelot-py.readthedocs.io/en/master/user/advanced.html#copy-text-in-spanning-cells)

`$camelot->copyTextSpanningCells('r', 'b')`


## License

MIT. Use at your own risk, we accept no liability for how this code is used.