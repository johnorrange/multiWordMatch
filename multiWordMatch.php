<?php


/*

Copyright (c) <2012, 2014> <John Orrange / Just Omniety>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/

//////////////////////////////////////////////////////////
//
// $exploded is an explode()'d text string haystack
// $needle is a space delimited multiword string to find
//
// returns FALSE or first occurance of a multiword phrase
//
//////////////////////////////////////////////////////////

function multiWordMatch($exploded, $needle)
{
   if(!$needle)
      return FALSE;

   $needle = array_filter(explode(" ", $needle));

   if(!$needle || count($needle) == 0)
      return FALSE;

   // word indices of the first word of the needle in the haystack
   $isect = array_keys($exploded, $needle[0]);

   $nl = count($needle);

   // if a one word needle and there are multiple matches, return the first index
   if($nl == 1 && count($isect) > 0)
      return $isect[0];


   // loop on partial matches of first word of the needle
   foreach($isect as $val)
   {
      // if the $nl array elements of the haystack starting at the partial match
      // match the needle, return the location
      if(count(array_diff(array_slice($exploded, $val, $nl), $needle)) == 0)
         return $val;
   }

   return FALSE;
}


   // get some test text

   $haystack = file_get_contents("http://loripsum.net/api/plaintext/verylong");
   $haystack = array_values(array_unique(array_filter(explode(" ", preg_replace('/[^a-z\d]+/i', ' ', $haystack)))));

   $test = array();

   for($x = 2; $x < 6; $x ++)
   {
      $arr = array_chunk($haystack, $x); // divide into $x word phrases
      array_pop($arr); // trim shorty
      $test[$x] = $arr;
   }
   
   foreach($test as $key => $val)
   {
      shuffle($val); // randomize order of phrases
      $test[$key] = $val;

      $cnt = count($val);

      for($x = 0; $x < $cnt; $x ++)
      {
         if(rand(1, 100) < 50) // randomize 50% of the phrases to prevent matches
            shuffle($test[$key][$x]);
         $test[$key][$x] = implode(" ", $test[$key][$x]);
      }
   }

   $cntHaystack = count($haystack);

   foreach($test as $key => $val)
   {
      $cntNeedle = count($val);

      echo "testing $cntNeedle $key-word phrases against a $cntHaystack word source.\n";

      $start = microtime(true);

      $matched = 0;

      for($x = 0; $x < $cntNeedle; $x++)
         if(multiWordMatch($haystack, $val[$x]) !== false)
            $matched ++;

      echo "$matched matches out of $cntNeedle\n";

      $stop = microtime(true);

      echo "average of " . (1000 * ($stop - $start) / $cntNeedle) . " seconds per thousand.\n";
      echo "---------------------------------------------------------\n";

   }

?>
