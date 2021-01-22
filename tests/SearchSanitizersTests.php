<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Etable\SearchSanitizers;

class SearchSanitizersTest extends TestCase
{
    public function testFilterStringWithScalar()
    {
        $input    = 123;
        $expected = "123";
        $output   = SearchSanitizers::filterString($input);

        $this->assertEquals($expected, $output);
    }

    public function testFilterStringWithString()
    {
        $input    = "Hello";
        $expected = "Hello";
        $output   = SearchSanitizers::filterString($input);

        $this->assertEquals($expected, $output);
    }

    public function testFilterStringWithNonStringOrSchalar()
    {
        $input  = [];
        $output = SearchSanitizers::filterString($input);

        $this->assertEquals('', $output);
    }

    public function testUnaccentGreekString()
    {
        $input    = "Ὦ κοινὸν αὐτάδελφον Ἰσμήνης κάρα,";
        $expected = "Ω κοινον αυταδελφον Ισμηνησ καρα,";

        $output = SearchSanitizers::unaccentGreekString($input);

        $this->assertEquals($expected, $output);
    }

    public function testUnaccentNonGreekString()
    {
        $input  = "are you talking to me bro";
        $output = SearchSanitizers::unaccentGreekString($input);

        $this->assertEquals($input, $output);
    }

    public function testRemoveExcessWhitespace()
    {
        $input    = "word1                   word2";
        $expected = "word1 word2";

        $output = SearchSanitizers::removeExcessWhitespace($input);
        $this->assertEquals($expected, $output);
    }

    public function testSanitizeSearchTermExcessWhitespace()
    {
        $input    = "word1                   word2";
        $expected = "word1 word2";

        $output = SearchSanitizers::sanitizeSearchTerm($input);

        $this->assertEquals($expected, $output);
    }


    public function testSanitizeSearchTermUnaccent()
    {
        $input    = "Ὦ κοινὸν αὐτάδελφον Ἰσμήνης κάρα,";
        $expected = "κοινον αυταδελφον ισμηνησ καρα";

        $output = SearchSanitizers::sanitizeSearchTerm($input);
        $this->assertEquals($expected, $output);
    }

    public function testSanitizeSpecialCharacters()
    {
        $input    = "!#().-_+&'\"`]";
        $expected = "";

        $output = SearchSanitizers::sanitizeSearchTerm($input);

        $this->assertEquals($expected, $output);
    }
}