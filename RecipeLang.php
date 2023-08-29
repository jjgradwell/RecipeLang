<?php
ini_set('display_errors', 1);

class RecipeLang
{

  public $dir;
  public $lang, $meta, $timers, $cookware, $ingredients, $details;

  function __construct() {
    $this->dir = "./rcp/";
    $this->frac = [
      "1/4" => "\u00bc", "1/2" => "\u00bd", "3/4" => "\u00be", "1/3" => "\u2153", "2/3" => "\u2154",
      "1/5" => "\u2155", "2/5" => "\u2156", "3/5" => "\u2157", "4/5" => "\u2158", "1/6" => "\u2159",
      "5/6" => "\u215a", "1/8" => "\u215b", "3/8" => "\u215c", "5/8" => "\u215d", "7/8" => "\u215e"
    ];

    $this->settings["en"] = [ "prep", "cook", "/ (seconds?)/iu", "/ (minutes?)/iu", "/ (hours?)/iu" ];
    $this->settings["fr"] = [ "préparation", "cuisson", "/ second(es|s)?/ui", "/ minutes?/iu", "/ heures?/iu" ];
  }

  function cook( $name ) {
    self::loadRecipe( $name );
    unset( $this->dir, $this->frac, $this->settings );
    self::showRecipe();
  }

  function convertToFractions( $data ) {
    foreach( $this->frac as $rep => $match ) $data = str_ireplace( $match, $rep, $data );
    return $data;
  }
  function convertToUnicode( $data ) {
    foreach( $this->frac as $rep => $match ) $data = str_ireplace( $rep, $match, $data );
    return $data;
  }

  private function loadRecipe( $name ) {
    $data = file_get_contents( sprintf( $this->dir . "%s.rcp", $name ) ); // Load file from disk
    self::findIncludes( $data ); // Look for included recipes within this recipe

    $data = self::convertToFractions( $data ); // Convert fractions
    $data = preg_replace( '/\r/', '', $data ); // Normalize line endings
    $data = preg_split( '/\n{3}/', $data ); // Split the meta and recipe info

    $this->meta = preg_split( '/\n/', $data[0] ); // Split metadata into individual lines
    $lang = array_values( preg_grep( '/@lang/iu', $this->meta ) );
    if ( isset( $lang[0] ) ) $this->lang = substr( $lang[0], -2 );
    else $this->lang = 'en';

    $this->data = $data[1]; // Put the actual recipe into a data variable

    self::findTimers(); // Find timers within the recipe
    self::findCookware(); // Find all the cookware in the recipe
    self::findSectionalIngredients(); // Find named ingredient sections
    self::findIngredients(); // Find recipe ingredients
  }

  private function findIncludes( &$data ) {
    preg_match_all( '/~\"(\w|\s|[,\.\/\-\&\%\|]){1,}\"/iu', $data, $found ); // Find all included recipes
    foreach( $found[0] as $match ) {
      $inc = file_get_contents( sprintf( $this->dir . "%s.rcp", substr( $match, 2, -1 ) ) ); // Load the recipe
      $inc = preg_split( '/\n{3}/', $inc ); // Split into meta and recipe info
      $data = str_replace( $match, "\n" . $inc[1], $data ); // Replace the include with recipe
    }
  }

  private function findTimers()
  {
    $timers  = array_slice( $this->settings[ $this->lang ], 0, 2 );
    list( $seconds, $minutes, $hours ) = array_slice( $this->settings[ $this->lang ], -3 );

    preg_match_all( '/@\((\w|\s|\.|\||[0-9]){1,}\)/iu', $this->data, $found ); // Find all timers
    foreach( $found[0] as $match ) { // Loop thru cookware
      $rep = explode( '|', substr( $match, 2, -1 ) ); // Split out timer with optional type
      if ( in_array( $rep[0], $timers ) ) {
        $type = $rep[0]; $val = $rep[1];
        $this->data = str_replace( $match, '', $this->data ); // Remove the counter from displaying
      } else {
        $type = $rep[1]; $val = $rep[0];
        $this->data = str_replace( $match, $val, $this->data ); // Replace the match with just the string
      }
      if ( !isset( $this->timers[ $type ] ) ) $this->timers[ $type ] = null;
      if ( preg_match( $seconds, $match ) ) {
        $this->timers[ $type ] += ( preg_replace( $seconds, '', $val ) / 60 );
      } elseif ( preg_match( $minutes, $match ) ) {
        $this->timers[ $type ] += preg_replace( $minutes, '', $val );
      } else if ( preg_match( $hours, $match ) ) {
        $this->timers[ $type ] += preg_replace( $hours, '', $val ) * 60;
      } else {
        $this->timers[ $type ] += $val;
      }
    }
  }

  private function findCookware()
  {
    preg_match_all( '/@\[(\w|\s|[,\.\/\-\&\%\|]){1,}\]/iu', $this->data, $found ); // Find all cookware
    foreach( $found[0] as $match ) { // Loop thru cookware
      $rep = explode( '|', substr( $match, 2, -1 ) ); // Split out cookware with optional values
      $this->data = str_replace( $match, $rep[0], $this->data ); // Replace the match with just the item
      $this->cookware[] = isset( $rep[1] ) ? sprintf( "%s (%s)", $rep[0], $rep[1] ) : $rep[0]; // Add to cookware list
    }
  }

  private function findIngredients() {
    foreach( $this->data as $key => &$data ) {
      $regex = '/@\{(\w|\s|[,\.\/\-\&\%\|\(|\)|\']){1,}\}/iu'; // Recipe ingredient regex
      do {
        preg_match_all( $regex, $data, $found ); // Find all ingredients
        foreach( $found[0] as $str ) {
          $result = preg_split( '/\|/', substr( $str, 2, -1 ) ); // Remove the indicators
          $data = str_replace( $str, $result[0], $data ); // Replace the str with just ingredient name

          self::processIngredient( $result ); // Process the ingredient
          if ( isset( $this->section[ $key ] ) ) { // If the section is named
            $this->ingredients[ $this->section[ $key ] ][] = $result; // Add to the section ingredient list
          } else {
            $this->ingredients[] = $result; // Add to the general ingredient list
          }
        }
      } while ( preg_match( $regex, $data ) == true ); // Continue until all have been found
      $this->details .= $data . "\n"; // Rebuild the recipe
    }
    $this->details = trim( $this->details ); // Remove trailing new line
    unset( $this->data, $this->section );// Remove temp variables
  }

  private function processIngredient( &$res ) {
    switch( count( $res ) ) {
      case 1; // Only ingredient name
        $res = [ null, $res[0] ]; // Add the empty amount
        break;
      case 2; // Ingredient and ??
        if ( preg_match( '/[0-9]{1,}/', $res[1] ) ) $res = [ $res[1], $res[0] ]; // Amount
        else $res = [ null, sprintf( '%s - %s', $res[0], $res[1] ) ]; // Method
        break;
      case 3; // Ingredient, amount and method present
        $res = [ $res[1], sprintf( '%s - %s', $res[0], $res[2] ) ];
        break;
    }
    $res = sprintf( "%s|%s", $res[0], $res[1] ); // Place ingredient in proper order
  }

  private function findSectionalIngredients() {
    preg_match_all( '/# (\w|_|\s){1,}\n/iu', $this->data, $found ); // Find named ingredient sections
    if ( is_array( $found[0] ) ) { // Sections found
      foreach( $found[0] as $named ) {
        $this->section[] = trim( str_replace( "# ", '', $named ) ); // Save the section name
        $this->data = str_replace( $named, '', $this->data ); // Remove the section from recipe
      }
      $this->data = preg_split( '/\n{2}/', $this->data ); // Split recipe into sections
    }
  }

  function shop( $name ) {
    if ( !is_array( $name ) ) $name = [ $name ];

    foreach( $name as $file ) {
      $data = file_get_contents( sprintf( $this->dir . "%s.rcp", $file ) ); // Load file from disk
      self::findIncludes( $data ); // Look for included recipes within this recipe
      $data = self::convertToFractions( $data ); // Convert fractions

      $regex = '/@\{(\w|\s|[,\.\/\-\&\%\|\(|\)|\']){1,}\}/iu'; // Recipe ingredient regex
      do {
        preg_match_all( $regex, $data, $found ); // Find all ingredients
        foreach( $found[0] as $str ) {
          $res = preg_split( '/\|/', substr( $str, 2, -1 ) ); // Remove the indicators
          $data = str_replace( $str, $res[0], $data ); // Replace the str with just ingredient name
          $res  = array_slice( array_merge( $res, [null, null]), 0, 2 ); // Grab first two elements of ingredient details

          if ( !isset( $this->list[ $res[0] ] ) ) $this->list[ $res[0] ] = null; // Add ingredient if new
          if ( preg_match( '/[0-9](?= [a-z])/iu', $res[1] ) ) { // Look for measurement
            $ret = self::splitStr( $res[1] ); // Split into amount and measurement
            $key = array_key_first( $ret ); // Get the measurement

            if ( !isset( $this->list[ $res[0] ][ $key ] ) ) $this->list[ $res[0] ][ $key ] = null; // Add measurement if new
            $this->list[ $res[0] ][ $key ] += $ret[ $key ]; // Otherwise add qty to list
          }  
        }
      } while ( preg_match( $regex, $data ) == true ); // Continue until all have been found
    }
    
    foreach( $this as $key => $v ) if ( $v == null ) unset( $this->$key );
    unset( $this->frac, $this->settings );
    ksort( $this->list, SORT_NATURAL | SORT_FLAG_CASE );
  }

  function splitStr( $str ) {
    $str = preg_split( '/ /', $str );
    $key = end( $str );
    if ( preg_match( '/\//', $str[0] ) ) {
      list( $n, $d ) = explode( '/', $str[0] );
      $str[0] = ( $n / $d );
    }
    if ( count( $str ) == 3 ) {
      list( $n, $d ) = explode( '/', $str[1] );
      $str[0] += ( $n / $d );
    }
    return [ $key => $str[0] ];
  }

  function showRecipe() {
    echo '<pre>', print_r( $this, 1 ), '</pre>';
  }
}
