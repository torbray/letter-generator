<?php
//============================================================+
// File name   : tcpdi_parser.php
// Version     : 1.1
// Begin       : 2013-09-25
// Last Update : 2016-05-03
// Author      : Paul Nicholls - https://github.com/pauln
// License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
//
// Based on    : tcpdf_parser.php
// Version     : 1.0.003
// Begin       : 2011-05-23
// Last Update : 2013-03-17
// Author      : Nicola Asuni - Tecnick.com LTD - www.tecnick.com - info@tecnick.com
// License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
// -------------------------------------------------------------------
// Copyright (C) 2011-2013 Nicola Asuni - Tecnick.com LTD
//
// This file is for use with the TCPDF software library.
//
// tcpdi_parser is free software: you can redistribute it and/or modify it
// under the terms of the GNU Lesser General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// tcpdi_parser is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU Lesser General Public License for more details.
//
// You should have received a copy of the License
// along with tcpdi_parser. If not, see
// <http://www.tecnick.com/pagefiles/tcpdf/LICENSE.TXT>.
//
// See LICENSE file for more information.
// -------------------------------------------------------------------
//
// Description : This is a PHP class for parsing PDF documents.
//
//============================================================+

/**
 * @file
 * This is a PHP class for parsing PDF documents.<br>
 * @author Paul Nicholls
 * @author Nicola Asuni
 * @version 1.1
 */

if (!defined('PDF_TYPE_NULL')) {
    define('PDF_TYPE_NULL', 0);
}
if (!defined('PDF_TYPE_NUMERIC')) {
    define('PDF_TYPE_NUMERIC', 1);
}
if (!defined('PDF_TYPE_TOKEN')) {
    define('PDF_TYPE_TOKEN', 2);
}
if (!defined('PDF_TYPE_HEX')) {
    define('PDF_TYPE_HEX', 3);
}
if (!defined('PDF_TYPE_STRING')) {
    define('PDF_TYPE_STRING', 4);
}
if (!defined('PDF_TYPE_DICTIONARY')) {
    define('PDF_TYPE_DICTIONARY', 5);
}
if (!defined('PDF_TYPE_ARRAY')) {
    define('PDF_TYPE_ARRAY', 6);
}
if (!defined('PDF_TYPE_OBJDEC')) {
    define('PDF_TYPE_OBJDEC', 7);
}
if (!defined('PDF_TYPE_OBJREF')) {
    define('PDF_TYPE_OBJREF', 8);
}
if (!defined('PDF_TYPE_OBJECT')) {
    define('PDF_TYPE_OBJECT', 9);
}
if (!defined('PDF_TYPE_STREAM')) {
    define('PDF_TYPE_STREAM', 10);
}
if (!defined('PDF_TYPE_BOOLEAN')) {
    define('PDF_TYPE_BOOLEAN', 11);
}
if (!defined('PDF_TYPE_REAL')) {
    define('PDF_TYPE_REAL', 12);
}

/**
 * @class tcpdi_parser
 * This is a PHP class for parsing PDF documents.<br>
 * Based on TCPDF_PARSER, part of the TCPDF project by Nicola Asuni.
 * @brief This is a PHP class for parsing PDF documents..
 * @version 1.1
 * @author Paul Nicholls - github.com/pauln
 * @author Nicola Asuni - info@tecnick.com
 */
class tcpdi_parser
{
    /**
     * Unique parser ID
     * @public
     */
    public $uniqueid = '';

    /**
     * Raw content of the PDF document.
     * @private
     */
    private $pdfdata;

    /**
     * XREF data.
     * @protected
     */
    protected $xref = [];

    /**
     * Object streams.
     * @protected
     */
    protected $objstreams = [];

    /**
     * Objects in objstreams.
     * @protected
     */
    protected $objstreamobjs = [];

    /**
     * List of seen XREF data locations.
     * @protected
     */
    protected $xref_seen_offsets = [];

    /**
     * Array of PDF objects.
     * @protected
     */
    protected $objects = [];

    /**
     * Array of object offsets.
     * @private
     */
    private $objoffsets = [];

    /**
     * Class object for decoding filters.
     * @private
     */
    private $FilterDecoders;

    /**
     * Pages
     *
     * @private array
     */
    private $pages;

    /**
     * Page count
     * @private integer
     */
    private $page_count;

    /**
     * actual page number
     * @private integer
     */
    private $pageno;

    /**
     * PDF version of the loaded document
     * @private string
     */
    private $pdfVersion;

    /**
     * Available BoxTypes
     *
     * @public array
     */
    public $availableBoxes = ['/MediaBox', '/CropBox', '/BleedBox', '/TrimBox', '/ArtBox'];

// -----------------------------------------------------------------------------

    /**
     * Parse a PDF document an return an array of objects.
     * @param $data (string) PDF data to parse.
     * @public
     * @throws TcpdiParserException
     * @since 1.0.000 (2011-05-24)
     */
    public function __construct($data, $uniqueid)
    {
        if (empty($data)) {
            $this->error('Empty PDF data.');
        }
        $this->uniqueid = $uniqueid;
        $this->pdfdata = $data;
        // get length
        // initialize class for decoding filters
        $this->FilterDecoders = new TCPDF_FILTERS();
        // get xref and trailer data
        $this->xref = $this->getXrefData();
        $this->findObjectOffsets();
        // parse all document objects
        $this->objects = [];
        $this->getPDFVersion();
        $this->readPages();
    }

    /**
     * Clean up when done, to free memory etc
     */
    public function cleanUp()
    {
        unset($this->pdfdata);
        $this->pdfdata = '';
        unset($this->objstreams);
        $this->objstreams = [];
        unset($this->objects);
        $this->objects = [];
        unset($this->objstreamobjs);
        $this->objstreamobjs = [];
        unset($this->xref);
        $this->xref = [];
        unset($this->objoffsets);
        $this->objoffsets = [];
        unset($this->pages);
        $this->pages = [];
    }

    /**
     * Return an array of parsed PDF document objects.
     * @return array Array of parsed PDF document objects.
     * @public
     * @since 1.0.000 (2011-06-26)
     */
    public function getParsedData(): array
    {
        return [$this->xref, $this->objects, $this->pages];
    }

    /**
     * Get PDF-Version
     *
     * And reset the PDF Version used in FPDI if needed
     * @public
     */
    public function getPDFVersion()
    {
        preg_match('/\d\.\d/', substr($this->pdfdata, 0, 16), $m);
        if (isset($m[0])) {
            $this->pdfVersion = $m[0];
        }
        return $this->pdfVersion;
    }

    /**
     * Read all /Page(es)
     *
     */
    function readPages()
    {
        $params = $this->getObjectVal($this->xref['trailer'][1]['/Root']);
        $objref = null;
        if ($params && $params[1] && is_array($params[1][1])) {
            foreach ($params[1][1] as $k => $v) {
                if ($k == '/Pages') {
                    $objref = $v;
                    break;
                }
            }
        }
        if ($objref == null || $objref[0] !== PDF_TYPE_OBJREF) {
            // Offset not found.
            return;
        }

        $dict = $this->getObjectVal($objref);
        if (is_array($dict) && $dict[0] == PDF_TYPE_OBJECT &&
            isset($dict[1]) && is_array($dict[1]) && $dict[1][0] == PDF_TYPE_DICTIONARY) {
            // Dict wrapped in an object
            $dict = $dict[1];
        }

        if (!is_array($dict) || $dict[0] !== PDF_TYPE_DICTIONARY) {
            return;
        }

        $this->pages = [];
        if (isset($dict[1]['/Kids']) && is_array($dict[1]['/Kids'])) {
            $v = $dict[1]['/Kids'];
            if ($v[0] == PDF_TYPE_ARRAY && isset($v[1]) && is_array($v[1])) {
                foreach ($v[1] as $ref) {
                    $page = $this->getObjectVal($ref);
                    $this->readPage($page);
                }
            }
        }

        $this->page_count = count($this->pages);
    }

    /**
     * Read a single /Page element, recursing through /Kids if necessary
     *
     */
    private function readPage($page)
    {
        if (isset($page[1][1]['/Kids'])) {
            // Nested pages!
            foreach ($page[1][1]['/Kids'][1] as $subref) {
                $subpage = $this->getObjectVal($subref);
                $this->readPage($subpage);
            }
        } else {
            $this->pages[] = $page;
        }
    }

    /**
     * Get pagecount from sourcefile
     *
     * @return int
     */
    public function getPageCount(): int
    {
        return $this->page_count;
    }

    /**
     * Get Cross-Reference (xref) table and trailer data from PDF document data.
     * @param $offset (int) xref offset (if know).
     * @param $xref (array) previous xref array (if any).
     * @return array Array containing xref and trailer data.
     * @protected
     * @throws TcpdiParserException
     * @since 1.0.000 (2011-05-24)
     */
    protected function getXrefData(int $offset = 0, array $xref = []): array
    {
        if ($offset == 0) {
            // find last startxref
            if (preg_match('/.*[\r\n]startxref[\s\r\n]+([0-9]+)[\s\r\n]+%%EOF/is', $this->pdfdata, $matches) == 0) {
                $this->error('Unable to find startxref');
            }
            $startxref = (int) $matches[1];
        } else {
            if (preg_match('/([0-9]+[\s][0-9]+[\s]obj)/i', $this->pdfdata, $matches, PREG_OFFSET_CAPTURE, $offset)) {
                // Cross-Reference Stream object
                $startxref = $offset;
            } elseif (preg_match(
                '/[\r\n]startxref[\s\r\n]+([0-9]+)[\s\r\n]+%%EOF/i',
                $this->pdfdata,
                $matches,
                PREG_OFFSET_CAPTURE,
                $offset
            )) {
                // startxref found
                $startxref = (int) $matches[1][0];
            } else {
                $this->error('Unable to find startxref');
            }
        }
        unset($matches);

        // DOMPDF gets the startxref wrong, giving us the linebreak before the xref starts.
        $startxref += strspn($this->pdfdata, "\r\n", $startxref);

        // check xref position
        if (strpos($this->pdfdata, 'xref', $startxref) == $startxref) {
            // Cross-Reference
            $xref = $this->decodeXref($startxref, $xref);
        } else {
            // Cross-Reference Stream
            $xref = $this->decodeXrefStream($startxref, $xref);
        }
        if (empty($xref)) {
            $this->error('Unable to find xref');
        }

        return $xref;
    }

    /**
     * Decode the Cross-Reference section
     * @param int $startxref Offset at which the xref section starts.
     * @param array $xref Previous xref array (if any).
     * @return array Array containing xref and trailer data.
     * @protected
     * @throws TcpdiParserException
     * @since 1.0.000 (2011-06-20)
     */
    protected function decodeXref(int $startxref, array $xref = []): array
    {
        $this->xref_seen_offsets[] = $startxref;
        if (!isset($xref['xref_location'])) {
            $xref['xref_location'] = $startxref;
            $xref['max_object'] = 0;
        }
        // extract xref data (object indexes and offsets)
        $xoffset = $startxref + 5;
        // initialize object number
        $obj_num = 0;
        $offset = $xoffset;
        while (preg_match(
                '/^([0-9]+)[\s]([0-9]+)[\s]?([nf]?)/im',
                $this->pdfdata,
                $matches,
                PREG_OFFSET_CAPTURE,
                $offset
            ) > 0) {
            $offset = strlen($matches[0][0]) + $matches[0][1];
            if (isset($matches[3][0]) && $matches[3][0] == 'n') {
                // create unique object index: [object number]_[generation number]
                $gen_num = intval($matches[2][0]);
                // check if object already exist
                if (!isset($xref['xref'][$obj_num][$gen_num])) {
                    // store object offset position
                    $xref['xref'][$obj_num][$gen_num] = intval($matches[1][0]);
                }
                ++$obj_num;
                $offset += 2;
            } elseif (isset($matches[3][0]) && $matches[3][0] == 'f') {
                ++$obj_num;
                $offset += 2;
            } else {
                // object number (index)
                $obj_num = intval($matches[1][0] ?? 0);
            }
        }
        unset($matches);
        $xref['max_object'] = max($xref['max_object'], $obj_num);
        // get trailer data
        if (preg_match(
                '/trailer[\s]*<<(.*)>>[\s\r\n]+(?:[%].*[\r\n]+)*startxref[\s\r\n]+/isU',
                $this->pdfdata,
                $matches,
                PREG_OFFSET_CAPTURE,
                $xoffset
            ) > 0) {
            $trailer_data = $matches[1][0];
            if (empty($xref['trailer'])) {
                // get only the last updated version
                $xref['trailer'] = [];
                $xref['trailer'][0] = PDF_TYPE_DICTIONARY;
                $xref['trailer'][1] = [];
                // parse trailer_data
                if (preg_match('/Size[\s]+([0-9]+)/i', $trailer_data, $matches) > 0) {
                    $xref['trailer'][1]['/Size'] = [PDF_TYPE_NUMERIC, intval($matches[1])];
                }
                if (preg_match('/Root[\s]+([0-9]+)[\s]+([0-9]+)[\s]+R/i', $trailer_data, $matches) > 0) {
                    $xref['trailer'][1]['/Root'] = [PDF_TYPE_OBJREF, intval($matches[1]), intval($matches[2])];
                }
                if (preg_match('/Encrypt[\s]+([0-9]+)[\s]+([0-9]+)[\s]+R/i', $trailer_data, $matches) > 0) {
                    $xref['trailer'][1]['/Encrypt'] = [PDF_TYPE_OBJREF, intval($matches[1]), intval($matches[2])];
                }
                if (preg_match('/Info[\s]+([0-9]+)[\s]+([0-9]+)[\s]+R/i', $trailer_data, $matches) > 0) {
                    $xref['trailer'][1]['/Info'] = [PDF_TYPE_OBJREF, intval($matches[1]), intval($matches[2])];
                }
                if (preg_match('/ID[\s]*[\[][\s]*[<]([^>]*)[>][\s]*[<]([^>]*)[>]/i', $trailer_data, $matches) > 0) {
                    $xref['trailer'][1]['/ID'] = [PDF_TYPE_ARRAY, []];
                    $xref['trailer'][1]['/ID'][1][0] = [PDF_TYPE_HEX, $matches[1]];
                    $xref['trailer'][1]['/ID'][1][1] = [PDF_TYPE_HEX, $matches[2]];
                }
            }
            if (preg_match('/Prev[\s]+([0-9]+)/i', $trailer_data, $matches) > 0) {
                // get previous xref
                $prevoffset = intval($matches[1]);
                if (!in_array($prevoffset, $this->xref_seen_offsets)) {
                    $this->xref_seen_offsets[] = $prevoffset;
                    $xref = $this->getXrefData($prevoffset, $xref);
                }
            }
            unset($matches);
        } else {
            $this->error('Unable to find trailer');
        }
        return $xref;
    }

    /**
     * Decode the Cross-Reference Stream section
     * @param int $startxref Offset at which the xref section starts.
     * @param array $xref Previous xref array (if any).
     * @return array Array containing xref and trailer data.
     * @protected
     * @since 1.0.003 (2013-03-16)
     */
    protected function decodeXrefStream(int $startxref, array $xref = []): array
    {
        // try to read Cross-Reference Stream
        [$xrefobj] = $this->getRawObject($startxref);
        $xrefcrs = $this->getIndirectObject((string) $xrefobj[1], $startxref);
        if (!isset($xref['xref_location'])) {
            $xref['xref_location'] = $startxref;
            $xref['max_object'] = 0;
        }
        if (!isset($xref['xref'])) {
            $xref['xref'] = [];
        }
        if (empty($xref['trailer'])) {
            // get only the last updated version
            $xref['trailer'] = [];
            $xref['trailer'][0] = PDF_TYPE_DICTIONARY;
            $xref['trailer'][1] = [];
            $filltrailer = true;
        } else {
            $filltrailer = false;
        }
        $valid_crs = false;
        $sarr = $xrefcrs[0][1];
        $keys = array_keys($sarr);
        $columns = 1; // Default as per PDF 32000-1:2008.
        $predictor = 1; // Default as per PDF 32000-1:2008.
        foreach ($keys as $key) {
            $v = $sarr[$key];
            if ($key === '/Type' && is_array($v) && $v[0] == PDF_TYPE_TOKEN && isset($v[1]) && $v[1] === 'XRef') {
                $valid_crs = true;
            } elseif ($key === '/Index' && is_array($v) && $v[0] == PDF_TYPE_ARRAY && isset($v[1]) &&  count($v[1]) >= 2) {
                // first object number in the subsection
                $index_first = intval($v[1][0][1]);
                // number of entries in the subsection
            } elseif ($key === '/Prev' && is_array($v) && $v[0] == PDF_TYPE_NUMERIC) {
                // get previous xref offset
                $prevxref = intval($v[1]);
            } elseif ($key === '/W' && is_array($v) && $v[0] == PDF_TYPE_ARRAY) {
                // number of bytes (in the decoded stream) of the corresponding field
                $wb = [];
                $wb[0] = intval($v[1][0][1]);
                $wb[1] = intval($v[1][1][1]);
                $wb[2] = intval($v[1][2][1]);
            } elseif ($key == '/DecodeParms' && is_array($v) && $v[0] == PDF_TYPE_DICTIONARY) {
                $decpar = $v[1];
                foreach ($decpar as $kdc => $vdc) {
                    if ($kdc == '/Columns' && $vdc[0] == PDF_TYPE_NUMERIC) {
                        $columns = intval($vdc[1]);
                    } elseif ($kdc == '/Predictor' && $vdc[0] == PDF_TYPE_NUMERIC) {
                        $predictor = intval($vdc[1]);
                    }
                }
            } elseif ($filltrailer) {
                switch ($key) {
                    case '/Size':
                    case '/Root':
                    case '/Info':
                    case '/ID':
                        $xref['trailer'][1][$key] = $v;
                        break;
                    default:
                        break;
                }
            }
        }
        // decode data
        $obj_num = 0;
        if ($valid_crs && isset($xrefcrs[1][3][0])) {
            // number of bytes in a row
            $rowlen = $columns + 1;
            // convert the stream into an array of integers
            $sdata = unpack('C*', $xrefcrs[1][3][0]);
            // split the rows
            $sdata = array_chunk($sdata, $rowlen);
            // initialize decoded array
            $ddata = [];
            // initialize first row with zeros
            $prev_row = array_fill(0, $rowlen, 0);
            // for each row apply PNG unpredictor
            foreach ($sdata as $k => $row) {
                // initialize new row
                $ddata[$k] = [];
                // get PNG predictor value
                if (empty($predictor)) {
                    $predictor = 10 + $row[0];
                }
                // for each byte on the row
                for ($i = 1; $i <= $columns; ++$i) {
                    if (!isset($row[$i])) {
                        // No more data in this row - we're done here.
                        break;
                    }
                    // new index
                    $j = $i - 1;
                    $row_up = $prev_row[$j];
                    if ($i == 1) {
                        $row_left = 0;
                        $row_upleft = 0;
                    } else {
                        $row_left = $row[($i - 1)];
                        $row_upleft = $prev_row[($j - 1)];
                    }
                    switch ($predictor) {
                        case 1: // No prediction (equivalent to PNG None)
                        case 10:
                            // PNG prediction (on encoding, PNG None on all rows)
                            $ddata[$k][$j] = $row[$i];
                            break;
                        case 11:
                            // PNG prediction (on encoding, PNG Sub on all rows)
                            $ddata[$k][$j] = $row[$i] + $row_left & 0xff;
                            break;
                        case 12:
                            // PNG prediction (on encoding, PNG Up on all rows)
                            $ddata[$k][$j] = $row[$i] + $row_up & 0xff;
                            break;
                        case 13:
                            // PNG prediction (on encoding, PNG Average on all rows)
                            $ddata[$k][$j] = $row[$i] + ($row_left + $row_up) / 2 & 0xff;
                            break;
                        case 14:
                            // PNG prediction (on encoding, PNG Paeth on all rows)
                            // initial estimate
                            $p = $row_left + $row_up - $row_upleft;
                            // distances
                            $pa = abs($p - $row_left);
                            $pb = abs($p - $row_up);
                            $pc = abs($p - $row_upleft);
                            $pmin = min($pa, $pb, $pc);
                            // return minumum distance
                            switch ($pmin) {
                                case $pa:
                                {
                                    $ddata[$k][$j] = $row[$i] + $row_left & 0xff;
                                    break;
                                }
                                case $pb:
                                {
                                    $ddata[$k][$j] = $row[$i] + $row_up & 0xff;
                                    break;
                                }
                                case $pc:
                                {
                                    $ddata[$k][$j] = $row[$i] + $row_upleft & 0xff;
                                    break;
                                }
                            }
                            break;
                        default:
                            // PNG prediction (on encoding, PNG optimum)
                            $this->error("Unknown PNG predictor $predictor");
                    }
                }
                $prev_row = $ddata[$k];
            } // end for each row
            // complete decoding
            unset($sdata);
            $sdata = [];
            // for every row
            foreach ($ddata as $k => $row) {
                // initialize new row
                $sdata[$k] = [0, 0, 0];
                if (!isset($wb[0]) || $wb[0] == 0) {
                    // default type field
                    $sdata[$k][0] = 1;
                }
                $i = 0; // count bytes on the row
                // for every column
                for ($c = 0; $c < 3; ++$c) {
                    // for every byte on the column
                    if (isset($wb[$c])) {
                        for ($b = 0; $b < $wb[$c]; ++$b) {
                            if (isset($row[$i])) {
                                $sdata[$k][$c] += $row[$i] << ($wb[$c] - 1 - $b) * 8;
                            }
                            ++$i;
                        }
                    }
                }
            }
            unset($ddata);
            // fill xref
            $obj_num = $index_first ?? 0;
            foreach ($sdata as $row) {
                switch ($row[0]) {
                    case 0:
                        // (f) linked list of free objects
                        ++$obj_num;
                        break;
                    case 1:
                        // (n) objects that are in use but are not compressed
                        // create unique object index: [object number]_[generation number]
                        // check if object already exist
                        if (!isset($xref['xref'][$obj_num][$row[2]])) {
                            // store object offset position
                            $xref['xref'][$obj_num][$row[2]] = $row[1];
                        }
                        ++$obj_num;
                        break;
                    case 2:
                        // compressed objects
                        // $row[1] = object number of the object stream in which this object is stored
                        // $row[2] = index of this object within the object stream
                        /*$index = $row[1].'_0_'.$row[2];
                        $xref['xref'][$row[1]][0][$row[2]] = -1;*/
                        break;
                    default:
                        // null objects
                        break;
                }
            }
        } // end decoding data
        $xref['max_object'] = max($xref['max_object'], $obj_num);
        if (isset($prevxref)) {
            // get previous xref
            $xref = $this->getXrefData($prevxref, $xref);
        }
        return $xref;
    }

    /**
     * Get raw stream data
     * @param int $offset Stream offset.
     * @param int $length Stream length.
     * @return array Steam content
     * @protected
     */
    protected function getRawStream(int $offset, int $length): array
    {
        $offset += strspn($this->pdfdata, "\x00\x09\x0a\x0c\x0d\x20", $offset);
        $offset += 6; // "stream"
        $offset += strspn($this->pdfdata, "\x20", $offset);
        $offset += strspn($this->pdfdata, "\r\n", $offset);

        $obj = [];
        $obj[] = PDF_TYPE_STREAM;
        $obj[] = substr($this->pdfdata, $offset, $length);

        return [$obj, $offset + $length];
    }

    /**
     * Get object type, raw value and offset to next object
     * @param int $offset Object offset.
     * @return array containing object type, raw value and offset to next object
     * @protected
     * @since 1.0.000 (2011-06-20)
     */
    protected function getRawObject(int $offset = 0, ?string $data = null): array
    {
        if ($data == null) {
            $data =& $this->pdfdata;
        }
        $objtype = ''; // object type to be returned
        $objval = ''; // object value to be returned
        // skip initial white space chars: \x00 null (NUL), \x09 horizontal tab (HT), \x0A line feed (LF), \x0C form feed (FF), \x0D carriage return (CR), \x20 space (SP)
        while (strspn($data[$offset], "\x00\x09\x0a\x0c\x0d\x20") == 1) {
            $offset++;
        }
        // get first char
        $char = $data[$offset];
        // get object type
        switch ($char) {
            case '%':
                // \x25 PERCENT SIGN
                // skip comment and search for next token
                $next = strcspn($data, "\r\n", $offset);
                if ($next > 0) {
                    $offset += $next;
                    return $this->getRawObject($offset, $data);
                }
                break;
            case '/':
                // \x2F SOLIDUS
                // name object
                $objtype = PDF_TYPE_TOKEN;
                ++$offset;
                $length = strcspn($data, "\x00\x09\x0a\x0c\x0d\x20\x28\x29\x3c\x3e\x5b\x5d\x7b\x7d\x2f\x25", $offset);
                $objval = substr($data, $offset, $length);
                $offset += $length;
                break;
            case '(':   // \x28 LEFT PARENTHESIS
            case ')':
                // \x29 RIGHT PARENTHESIS
                // literal string object
                $objtype = PDF_TYPE_STRING;
                ++$offset;
                $strpos = $offset;
                if ($char == '(') {
                    $open_bracket = 1;
                    while ($open_bracket > 0) {
                        if (!isset($data[$strpos])) {
                            break;
                        }
                        $ch = $data[$strpos];
                        switch ($ch) {
                            case '\\':
                                // REVERSE SOLIDUS (5Ch) (Backslash)
                                // skip next character
                                ++$strpos;
                                break;
                            case '(':
                                // LEFT PARENHESIS (28h)
                                ++$open_bracket;
                                break;
                            case ')':
                                // RIGHT PARENTHESIS (29h)
                                --$open_bracket;
                                break;
                        }
                        ++$strpos;
                    }
                    $objval = substr($data, $offset, $strpos - $offset - 1);
                    $offset = $strpos;
                }
                break;
            case '[':   // \x5B LEFT SQUARE BRACKET
            case ']':
                // \x5D RIGHT SQUARE BRACKET
                // array object
                $objtype = PDF_TYPE_ARRAY;
                ++$offset;
                if ($char == '[') {
                    // get array content
                    $objval = [];
                    do {
                        // get element
                        [$element, $offset] = $this->getRawObject($offset, $data);
                        $objval[] = $element;
                    } while ($element[0] !== ']');
                    // remove closing delimiter
                    array_pop($objval);
                } else {
                    $objtype = ']';
                }
                break;
            case '<':   // \x3C LESS-THAN SIGN
            case '>':
                // \x3E GREATER-THAN SIGN
                if (isset($data[($offset + 1)]) && $data[($offset + 1)] == $char) {
                    // dictionary object
                    $objtype = PDF_TYPE_DICTIONARY;
                    if ($char == '<') {
                        list ($objval, $offset) = $this->getDictValue($offset, $data);
                    } else {
                        $objtype = '>>';
                        $offset += 2;
                    }
                } else {
                    // hexadecimal string object
                    $objtype = PDF_TYPE_HEX;
                    ++$offset;
                    // The "Panose" entry in the FontDescriptor Style dict seems to have hex bytes separated by spaces.
                    if ($char == '<' && preg_match(
                                '/^([0-9A-Fa-f ]+)[>]/iU',
                                substr($data, $offset),
                                $matches
                            ) == 1) {
                        $objval = $matches[1];
                        $offset += strlen($matches[0]);
                        unset($matches);
                    }
                }
                break;
            default:
                $frag = $data[$offset] . @$data[$offset + 1] . @$data[$offset + 2] . @$data[$offset + 3];
                switch ($frag) {
                    case 'endo':
                        // indirect object
                        $objtype = 'endobj';
                        $offset += 6;
                        break;
                    case 'stre':
                        // Streams should always be indirect objects, and thus processed by getRawStream().
                        // If we get here, treat it as a null object as something has gone wrong.
                    case 'null':
                        // null object
                        $objtype = PDF_TYPE_NULL;
                        $offset += 4;
                        $objval = 'null';
                        break;
                    case 'true':
                        // boolean true object
                        $objtype = PDF_TYPE_BOOLEAN;
                        $offset += 4;
                        $objval = true;
                        break;
                    case 'fals':
                        // boolean false object
                        $objtype = PDF_TYPE_BOOLEAN;
                        $offset += 5;
                        $objval = false;
                        break;
                    case 'ends':
                        // end stream object
                        $objtype = 'endstream';
                        $offset += 9;
                        break;
                    default:
                        if (preg_match(
                                '/^([0-9]+)[\s]+([0-9]+)[\s]+([Robj]{1,3})/i',
                                substr($data, $offset, 33),
                                $matches
                            ) == 1) {
                            if ($matches[3] == 'R') {
                                // indirect object reference
                                $objtype = PDF_TYPE_OBJREF;
                                $offset += strlen($matches[0]);
                                $objval = [intval($matches[1]), intval($matches[2])];
                            } elseif ($matches[3] == 'obj') {
                                // object start
                                $objtype = PDF_TYPE_OBJECT;
                                $objval = intval($matches[1]) . '_' . intval($matches[2]);
                                $offset += strlen($matches[0]);
                            }
                        } elseif (($numlen = strspn($data, '+-.0123456789', $offset)) > 0) {
                            // numeric object
                            $objval = substr($data, $offset, $numlen);
                            $objtype = intval($objval) != $objval ? PDF_TYPE_REAL : PDF_TYPE_NUMERIC;
                            $offset += $numlen;
                        }
                        unset($matches);
                        break;
                break;
            }
        }
        $obj = [];
        $obj[] = $objtype;
        if ($objtype == PDF_TYPE_OBJREF && is_array($objval)) {
            foreach ($objval as $val) {
                $obj[] = $val;
            }
        } else {
            $obj[] = $objval;
        }
        return [$obj, $offset];
    }

    private function getDictValue(int $offset, string $data): array
    {
        $objval = [];

        // Extract dict from data.
        $i = 1;
        $dict = '';
        $offset += 2;
        do {
            if ($data[$offset] == '>' && $data[$offset + 1] == '>') {
                $i--;
                $dict .= '>>';
                $offset += 2;
            } else {
                if ($data[$offset] == '<' && $data[$offset + 1] == '<') {
                    $i++;
                    $dict .= '<<';
                    $offset += 2;
                } else {
                    $dict .= $data[$offset];
                    $offset++;
                }
            }
        } while ($i > 0);

        // Now that we have just the dict, parse it.
        $dictoffset = 0;
        do {
            // Get dict element.
            [$key, $eloffset] = $this->getRawObject($dictoffset, $dict);
            if ($key[0] == '>>') {
                break;
            }
            [$element, $dictoffset] = $this->getRawObject($eloffset, $dict);
            $objval['/' . $key[1]] = $element;
            unset($key);
            unset($element);
        } while (true);

        return [$objval, $offset];
    }

    /**
     * Get content of indirect object.
     * @param string $obj_ref Object number and generation number separated by underscore character.
     * @param int $offset Object offset.
     * @param bool $decoding If true decode streams.
     * @return array containing object data.
     * @protected
     * @since 1.0.000 (2011-05-24)
     */
    protected function getIndirectObject(string $obj_ref, int $offset = 0, bool $decoding = true): array
    {
        $obj = explode('_', $obj_ref);
        if ($obj === false || count($obj) != 2) {
            $this->error('Invalid object reference: ' . $obj);
        }
        $objref = $obj[0] . ' ' . $obj[1] . ' obj';

        if (strpos($this->pdfdata, $objref, $offset) != $offset) {
            // an indirect reference to an undefined object shall be considered a reference to the null object
            return ['null', 'null', $offset];
        }
        // starting position of object content
        $offset += strlen($objref);
        // get array of object content
        $objdata = [];
        $i = 0; // object main index
        do {
            if ($i > 0 && isset($objdata[($i - 1)][0]) && $objdata[($i - 1)][0] == PDF_TYPE_DICTIONARY && array_key_exists(
                    '/Length',
                    $objdata[($i - 1)][1]
                )) {
                // Stream - get using /Length in stream's dict
                $lengthobj = $objdata[($i - 1)][1]['/Length'];
                if ($lengthobj[0] === PDF_TYPE_OBJREF) {
                    $lengthobj = $this->getObjectVal($lengthobj);
                    if ($lengthobj[0] === PDF_TYPE_OBJECT) {
                        $lengthobj = $lengthobj[1];
                    }
                }
                $streamlength = (int) $lengthobj[1];
                [$element, $offset] = $this->getRawStream($offset, $streamlength);
            } else {
                // get element
                [$element, $offset] = $this->getRawObject($offset);
            }
            // decode stream using stream's dictionary information
            if ($decoding && $element[0] == PDF_TYPE_STREAM && isset($objdata[($i - 1)][0]) && $objdata[($i - 1)][0] == PDF_TYPE_DICTIONARY) {
                $element[3] = $this->decodeStream($objdata[($i - 1)][1], $element[1]);
            }
            $objdata[$i] = $element;
            ++$i;
        } while ($element[0] != 'endobj');
        // remove closing delimiter
        array_pop($objdata);
        // return raw object content
        return $objdata;
    }

    /**
     * Get the content of object, resolving indect object reference if necessary.
     * @param array $obj Object value.
     * @return array containing object data.
     * @public
     * @since 1.0.000 (2011-06-26)
     */
    public function getObjectVal(array $obj)
    {
        if ($obj[0] == PDF_TYPE_OBJREF) {
            if (strpos($obj[1], '_') !== false) {
                $key = explode('_', $obj[1]);
            } else {
                $key = [$obj[1], $obj[2]];
            }

            $ret = [0 => PDF_TYPE_OBJECT, 'obj' => $key[0], 'gen' => $key[1]];

            // reference to indirect object
            $object = null;
            if (isset($this->objects[$key[0]][$key[1]])) {
                // this object has been already parsed
                $object = $this->objects[$key[0]][$key[1]];
            } elseif (($offset = $this->findObjectOffset($key)) !== false) {
                // parse new object
                $this->objects[$key[0]][$key[1]] = $this->getIndirectObject($key[0] . '_' . $key[1], $offset, false);
                $object = $this->objects[$key[0]][$key[1]];
            } elseif ($key[1] == 0 && isset($this->objstreamobjs[$key[0]])) {
                // Object is in an object stream
                $streaminfo = $this->objstreamobjs[$key[0]];
                $objs = $streaminfo[0];
                if (!isset($this->objstreams[$objs[0]][$objs[1]])) {
                    // Fetch and decode object stream
                    $objstream = $this->getObjectVal([PDF_TYPE_OBJREF, $objs[0], $objs[1]]);
                    $decoded = $this->decodeStream($objstream[1][1], $objstream[2][1]);
                    $this->objstreams[$objs[0]][$objs[1]] = $decoded[0]; // Store just the data, in case we need more from this objstream
                    // Free memory
                    unset($objstream);
                    unset($decoded);
                }
                $this->objects[$key[0]][$key[1]] = $this->getRawObject(
                    $streaminfo[1],
                    $this->objstreams[$objs[0]][$objs[1]]
                );
                $object = $this->objects[$key[0]][$key[1]];
            }
            if (!is_null($object)) {
                $ret[1] = $object[0];
                if (isset($object[1][0]) && $object[1][0] == PDF_TYPE_STREAM) {
                    $ret[0] = PDF_TYPE_STREAM;
                    $ret[2] = $object[1];
                }
                return $ret;
            }
        }
        return $obj;
    }

    /**
     * Extract object stream to find out what it contains.
     *
     */
    public function extractObjectStream($key)
    {
        $objref = [PDF_TYPE_OBJREF, $key[0], $key[1]];
        $obj = $this->getObjectVal($objref);
        if ($obj[0] !== PDF_TYPE_STREAM || !isset($obj[1][1]['/First'][1])) {
            // Not a valid object stream dictionary - skip it.
            return;
        }
        $stream = $this->decodeStream($obj[1][1], $obj[2][1]);// Decode object stream, as we need the first bit
        $first = intval($obj[1][1]['/First'][1]);
        $ints = preg_split('/\s/', substr($stream[0], 0, $first)); // Get list of object / offset pairs
        for ($j = 1; $j < count($ints); $j++) {
            if ($j % 2 == 1) {
                $this->objstreamobjs[$ints[$j - 1]] = [$key, $ints[$j] + $first];
            }
        }

        // Free memory - we may not need this at all.
        unset($obj);
        unset($stream);
    }

    /**
     * Find all object offsets.  Saves having to scour the file multiple times.
     * @private
     */
    private function findObjectOffsets()
    {
        $this->objoffsets = [];
        if (preg_match_all(
                '/(*ANYCRLF)^[\s]*([0-9]+)[\s]+([0-9]+)[\s]+obj/im',
                $this->pdfdata,
                $matches,
                PREG_OFFSET_CAPTURE
            ) >= 1) {
            $i = 0;
            $laststreamend = 0;
            foreach ($matches[0] as $match) {
                $offset = $match[1] + strspn($match[0], "\x00\x09\x0a\x0c\x0d\x20");
                if ($offset < $laststreamend) {
                    // Contained within another stream, skip it.
                    continue;
                }
                $this->objoffsets[trim($match[0])] = $offset;
                $dictoffset = $match[1] + strlen($match[0]);
                $dictfrag = substr($this->pdfdata, $dictoffset, 256);
                if (preg_match('|^\s+<<[^>]+/Length\s+(\d+)|', $dictfrag, $lengthmatch, PREG_OFFSET_CAPTURE) == 1) {
                    $laststreamend += intval($lengthmatch[1][0]);
                }
                if (preg_match('|^\s+<<[^>]+/ObjStm|', $dictfrag, $objstm) == 1) {
                    $this->extractObjectStream([$matches[1][$i][0], $matches[2][$i][0]]);
                }
                $i++;
            }
        }
        unset($lengthmatch);
        unset($dictfrag);
        unset($matches);
    }

    /**
     * Get offset of an object.  Checks xref first, then offsets found by scouring the file.
     * @param array $key Object key to find (obj, gen).
     * @return int|bool Offset of the object in $this->pdfdata.
     * @private
     */
    private function findObjectOffset(array $key)
    {
        $objref = $key[0] . ' ' . $key[1] . ' obj';
        if (isset($this->xref['xref'][$key[0]][$key[1]])) {
            $offset = $this->xref['xref'][$key[0]][$key[1]];
            if (strpos($this->pdfdata, $objref, $offset) === $offset) {
                // Offset is in xref table and matches actual position in file
                //echo "Offset in XREF is correct, returning<br>";
                return (int) $this->xref['xref'][$key[0]][$key[1]];
            }
        }
        if (array_key_exists($objref, $this->objoffsets)) {
            //echo "Offset found in internal reftable<br>";
            return (int) $this->objoffsets[$objref];
        }
        return false;
    }

    /**
     * Decode the specified stream.
     * @param array $sdic Stream's dictionary array.
     * @param string $stream Stream to decode.
     * @return array containing decoded stream data and remaining filters.
     * @protected
     * @since 1.0.000 (2011-06-22)
     */
    protected function decodeStream(array $sdic, string $stream): array
    {
        // get stream lenght and filters
        $slength = strlen($stream);
        if ($slength <= 0) {
            return ['', []];
        }
        $filters = [];
        foreach ($sdic as $k => $v) {
            if ($v[0] == PDF_TYPE_TOKEN) {
                if ($k === '/Length' && $v[0] == PDF_TYPE_NUMERIC) {
                    // get declared stream lenght
                    $declength = intval($v[1]);
                    if ($declength < $slength) {
                        $stream = substr($stream, 0, $declength);
                        $slength = $declength;
                    }
                } elseif ($k === '/Filter') {
                    // single filter
                    $filters[] = $v[1];
                }
            }
        }
        // decode the stream
        $remaining_filters = [];
        foreach ($filters as $filter) {
            if (in_array($filter, $this->FilterDecoders->getAvailableFilters())) {
                $stream = $this->FilterDecoders->decodeFilter($filter, $stream);
            } else {
                // add missing filter to array
                $remaining_filters[] = $filter;
            }
        }
        return [$stream, $remaining_filters];
    }


    /**
     * Set pageno
     *
     * @param int $pageno Pagenumber to use
     * @throws TcpdiParserException
     */
    public function setPageno(int $pageno): void
    {
        $pageno = $pageno - 1;

        if ($pageno < 0 || $pageno >= $this->getPageCount()) {
            $this->error("Pagenumber is wrong! (Requested $pageno, max " . $this->getPageCount() . ")");
        }

        $this->pageno = $pageno;
    }

    /**
     * Get page-resources from current page
     *
     * @return array
     */
    public function getPageResources()
    {
        return $this->_getPageResources($this->pages[$this->pageno]);
    }

    /**
     * Get page-resources from /Page
     *
     * @param array $obj Array of pdf-data
     */
    private function _getPageResources(array $obj)
    { // $obj = /Page
        $obj = $this->getObjectVal($obj);

        // If the current object has a resources
        // dictionary associated with it, we use
        // it. Otherwise, we move back to its
        // parent object.
        if (isset ($obj[1][1]['/Resources'])) {
            $res = $obj[1][1]['/Resources'];
            if (is_array($res) && $res[0] == PDF_TYPE_OBJECT) {
                return $res[1];
            }
            return $res;
        } else {
            if (!isset ($obj[1][1]['/Parent'])) {
                return false;
            } else {
                $res = $this->_getPageResources($obj[1][1]['/Parent']);
                if (is_array($res) && $res[0] == PDF_TYPE_OBJECT) {
                    return $res[1];
                }
                return $res;
            }
        }
    }

    /**
     * Get annotations from current page
     *
     * @return array|bool
     */
    public function getPageAnnotations()
    {
        return $this->_getPageAnnotations($this->pages[$this->pageno]);
    }

    /**
     * Get annotations from /Page
     *
     * @param array|bool $obj Array of pdf-data
     */
    private function _getPageAnnotations(array $obj)
    {
        // $obj = /Page
        $obj = $this->getObjectVal($obj);

        // If the current object has an annotations
        // dictionary associated with it, we use
        // it. Otherwise, we move back to its
        // parent object.
        if (isset ($obj[1][1]['/Annots'])) {
            $annots = $obj[1][1]['/Annots'];
        } else {
            if (!isset ($obj[1][1]['/Parent'])) {
                return false;
            }
            $annots = $this->_getPageAnnotations($obj[1][1]['/Parent']);
        }

        if (is_array($annots) && $annots[0] == PDF_TYPE_OBJREF) {
            return $this->getObjectVal($annots);
        }
        return $annots;
    }


    /**
     * Get content of current page
     *
     * If more /Contents is an array, the streams are concated
     *
     * @return string
     */
    public function getContent(): string
    {
        $buffer = '';

        if (isset($this->pages[$this->pageno][1][1]['/Contents']) &&
            is_array($this->pages[$this->pageno][1][1]['/Contents'])) {
            $contents = $this->_getPageContent($this->pages[$this->pageno][1][1]['/Contents']);
            foreach ($contents as $tmp_content) {
                $buffer .= $this->_rebuildContentStream($tmp_content) . ' ';
            }
        }

        return $buffer;
    }


    /**
     * Resolve all content-objects
     *
     * @param array $content_ref
     * @return array
     */
    private function _getPageContent(array $content_ref): array
    {
        $contents = [];

        if ($content_ref[0] == PDF_TYPE_OBJREF) {
            $content = $this->getObjectVal($content_ref);
            if (isset($content[1]) && is_array($content[1]) && $content[1][0] == PDF_TYPE_ARRAY) {
                $contents = $this->_getPageContent($content[1]);
            } else {
                $contents[] = $content;
            }
        } elseif ($content_ref[0] == PDF_TYPE_ARRAY &&
            isset($content_ref[1]) &&
            is_array($content_ref[1])) {

            foreach ($content_ref[1] as $tmp_content_ref) {
                $contents = array_merge($contents, $this->_getPageContent($tmp_content_ref));
            }
        }

        return $contents;
    }


    /**
     * Rebuild content-streams
     *
     * @param array $obj
     * @return string
     */
    private function _rebuildContentStream(array $obj)
    {
        $filters = [];

        if (isset($obj[1][1]['/Filter']) && is_array($obj[1][1]['/Filter'])) {
            $_filter = $obj[1][1]['/Filter'];

            if ($_filter[0] == PDF_TYPE_OBJREF) {
                $tmpFilter = $this->getObjectVal($_filter);
                $_filter = $tmpFilter[1];
            }

            if ($_filter[0] == PDF_TYPE_TOKEN) {
                $filters[] = $_filter;
            } elseif ($_filter[0] == PDF_TYPE_ARRAY) {
                $filters = $_filter[1];
            }
        }

        $stream = $obj[2][1] ?? '';

        foreach ($filters as $_filter) {
            $stream = $this->FilterDecoders->decodeFilter($_filter[1], $stream);
        }

        return $stream;
    }


    /**
     * Get a Box from a page
     * Arrayformat is same as used by fpdf_tpl
     *
     * @param array $page a /Page
     * @param string $box_index Type of Box @see $availableBoxes
     * @param float Scale factor from user space units to points
     * @return array|bool
     */
    public function getPageBox(array $page, string $box_index, float $k)
    {
        $page = $this->getObjectVal($page);
        $box = null;
        if (isset($page[1][1][$box_index])) {
            $box =& $page[1][1][$box_index];
        }

        if (!is_null($box) && $box[0] == PDF_TYPE_OBJREF) {
            $tmp_box = $this->getObjectVal($box);
            $box = $tmp_box[1];
        }

        if (!is_null($box) && $box[0] == PDF_TYPE_ARRAY) {
            $b =& $box[1];
            return [
                'x' => $b[0][1] / $k,
                'y' => $b[1][1] / $k,
                'w' => abs($b[0][1] - $b[2][1]) / $k,
                'h' => abs($b[1][1] - $b[3][1]) / $k,
                'llx' => min($b[0][1], $b[2][1]) / $k,
                'lly' => min($b[1][1], $b[3][1]) / $k,
                'urx' => max($b[0][1], $b[2][1]) / $k,
                'ury' => max($b[1][1], $b[3][1]) / $k,
            ];
        }
        if (!isset ($page[1][1]['/Parent'])) {
            return false;
        }
        return $this->getPageBox($this->getObjectVal($page[1][1]['/Parent']), $box_index, $k);
    }

    /**
     * Get all page boxes by page no
     *
     * @param int The page number
     * @param float Scale factor from user space units to points
     * @return array
     */
    public function getPageBoxes(int $pageno, float $k): array
    {
        return $this->_getPageBoxes($this->pages[$pageno - 1], $k);
    }

    /**
     * Get all boxes from /Page
     *
     * @param array a /Page
     * @return array
     */
    private function _getPageBoxes(array $page, float $k): array
    {
        $boxes = [];

        foreach ($this->availableBoxes as $box) {
            if ($_box = $this->getPageBox($page, $box, $k)) {
                $boxes[$box] = $_box;
            }
        }

        return $boxes;
    }

    /**
     * Get the page rotation by pageno
     *
     * @param int $pageno
     * @return array|bool
     */
    public function getPageRotation(int $pageno)
    {
        return $this->_getPageRotation($this->pages[$pageno - 1]);
    }

    private function _getPageRotation(array $obj)
    {
        // $obj = /Page
        $obj = $this->getObjectVal($obj);
        if (isset ($obj[1][1]['/Rotate'])) {
            $res = $this->getObjectVal($obj[1][1]['/Rotate']);
            if (is_array($res) && $res[0] == PDF_TYPE_OBJECT) {
                return $res[1];
            }
            return $res;
        }
        if (!isset ($obj[1][1]['/Parent'])) {
            return false;
        }
        $res = $this->_getPageRotation($obj[1][1]['/Parent']);
        if (is_array($res) && $res[0] == PDF_TYPE_OBJECT) {
            return $res[1];
        }
        return $res;
    }

    /**
     * This method is automatically called in case of fatal error; it simply outputs the message and halts the execution.
     * @param $msg (string) The error message
     * @public
     * @throws TcpdiParserException
     * @since 1.0.000 (2011-05-23)
     */
    public function error($msg)
    {
        throw new TcpdiParserException($msg);
    }

} // END OF TCPDF_PARSER CLASS

class TcpdiParserException extends Exception
{
}

//============================================================+
// END OF FILE
//============================================================+