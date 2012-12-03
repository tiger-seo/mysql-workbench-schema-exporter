<?php
/*
 * The MIT License
 *
 * Copyright (c) 2010 Johannes Mueller <circus2(at)web.de>
 * Copyright (c) 2012 Toha <tohenk@yahoo.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace MwbExporter\Formatter\Doctrine2\Yaml\Model;

use MwbExporter\Model\Column as Base;
use MwbExporter\Writer\WriterInterface;

class Column extends Base
{
    public function write(WriterInterface $writer)
    {
        $nullable = $this->parameters->get('isNotNull') == '0';
        $writer
            ->write('%s:', $this->getCamesCaseColumnName())
            ->indent()
                ->write('type: %s', $this->getDocument()->getFormatter()->getDatatypeConverter()->getType($this))
                ->writeIf($this->isString() && $this->getParameters()->get('length') > 0, 'length: %s', $this->getParameters()->get('length'))
                ->writeIf($this->isInteger(), 'unsigned: %s', $this->isUnsigned() ? 'true' : 'false')
                ->writeIf($this->isPrimary(), 'primary: true')
                ->write('nullable: %s', $nullable ? 'true' : 'false')
                ->writeCallback(function(WriterInterface $writer, Column $_this = null) {
                    if ($_this->getParameters()->get('autoIncrement') == 1) {
                        $writer
                            ->write('generator:')
                            ->indent()
                                ->write('strategy: IDENTITY')
                            ->outdent()
                        ;
                    }
                })
                ->writeIf(($default = $this->getParameters()->get('defaultValue')) && 'NULL' !== $default, 'default: '.$default)
                ->writeCallback(function(WriterInterface $writer, Column $_this = null) {
                    foreach ($_this->getNode()->xpath("value[@key='flags']/value") as $flag) {
                        $writer->write(strtolower($flag).': true');
                    }
                })
            ->outdent()
        ;

        return $this;
    }

    private function isString()
    {
        return $this->getDocument()->getFormatter()->getDatatypeConverter()->getType($this) == 'string';
    }

    private function isInteger()
    {
        return $this->getDocument()->getFormatter()->getDatatypeConverter()->getType($this) == 'integer';
    }

    private function isUnsigned()
    {
        return in_array('UNSIGNED', $this->parameters->get('flags'));
    }

    private function getCamesCaseColumnName()
    {
        return lcfirst($this->columnNameBeautifier($this->getColumnName()));
    }
}
