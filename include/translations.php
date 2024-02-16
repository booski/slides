<?php

$i18n = array(
    "Access denied." => array(
        "en" => function() { return "Access denied."; },
        "sv" => function() { return "Åtkomst nekad."; },
    ),
    "Database error: {error}" => array(
        "en" => function($error) { return "Database error: $error"; },
        "sv" => function($error) { return "Databasfel: $error"; },
    ),
    "Failed to connect to db. The error was: {error}" => array(
        "en" => function($error) {
            return "Failed to connect to database. The error was: $error";
        },
        "sv" => function($error) {
            return "Kunde inte ansluta till databasen. Felmeddelande: $error";
        },
    ),
    "Autoremoval failed." => array(
        "en" => function() { return "Autoremoval failed."; },
        "sv" => function() { return "Automatisk borttagning misslyckades."; },
    ),
    "Failed to prepare statement: {statement} {errorcode} {error}" => array(
        "en" => function($statement, $errorcode, $error) {
            return "Failed to prepare the following statement: $statement"
                  ."<br/>$errorcode: $error";
        },
        "sv" => function($statement, $errorcode, $error) {
            return "Failed to prepare the following statement: $statement"
                  ."<br/>$errorcode: $error";
        },
    ),
    ":(\nDatabase error" => array(
        "en" => function() { return ":(\nDatabase error"; },
        "sv" => function() { return ":(\nDatabasfel"; },
    ),
    ":(\nNot found" => array(
        "en" => function() { return ":(\nNot found"; },
        "sv" => function() { return ":(\nKunde inte hitta filen"; },
    ),
    "The slideshow must have a name." => array(
        "en" => function() { return "The slideshow must have a name."; },
        "sv" => function() { return "Ytan måste ha ett namn."; },
    ),
    "Both width and height are mandatory." => array(
        "en" => function() { return "Both width and height are mandatory."; },
        "sv" => function() { return "Både bredd och höjd måste anges."; },
    ),
    "Invalid width." => array(
        "en" => function() { return "Invalid width."; },
        "sv" => function() { return "Ogiltig bredd."; },
    ),
    "Invalid height." => array(
        "en" => function() { return "Invalid height."; },
        "sv" => function() { return "Ogiltig höjd."; },
    ),
    "Invalid time." => array(
        "en" => function() { return "Invalid time."; },
        "sv" => function() { return "Ogiltig tid."; },
    ),
    "The picture is in use in one or more slideshows." => array(
        "en" => function() {
            return "The picture is in use in one or more slideshows.";
        },
        "sv" => function() {
            return "Bilden används på en eller flera ytor.";
        },
    ),
    "The file could not be uploaded. (Error code: {error})" => array(
        "en" => function($error) {
            return "The file could not be uploaded. (Error code: $error)";
        },
        "sv" => function($error) {
            return "Filen kunde inte laddas upp. (Felkod: $error)";
        },
    ),
    "Invalid file type {mime}" => array(
        "en" => function($mime) {
            return "Invalid file type ($mime). "
                  ."You can only upload pictures and video here.";
        },
        "sv" => function($mime) {
            return "Ogiltig filtyp ($mime). "
                  ."Du kan bara ladda upp bilder och video här.";
        },
    ),
    "Invalid format {mime}. Allowed formats are {formats}" => array(
        "en" => function($mime, $formats) {
            return "Invalid format ($mime). Allowed formats are $formats";
        },
        "sv" => function($mime, $formats) {
            return "Ogiltigt format ($mime). Tillåtna format är $formats.";
        },
    ),
    "Image could not be read. (Error message: {error})" => array(
        "en" => function($error) {
            return "Image could not be read. (Error message: $error)";
        },
        "sv" => function($error) {
            return "Bilden kunde inte läsas. (Felmeddelande: $error)";
        },
    ),
    "Could not save video. {error} {errorcode}" => array(
        "en" => function($errormsg, $errornum) {
            return "Could not save video."
                  ."<br/>Error message: $errormsg"
                  ."<br/>Error code: $errornum";
        },
        "sv" => function($errormsg, $errornum) {
            return "Videon kunde inte sparas."
                  ."<br/>Felmeddelande: $errormsg"
                  ."<br/>Felkod: $errornum";
        },
    ),
    "File could not be saved. {error} {errorcode}" => array(
        "en" => function() {
            return "File could not be saved."
                  ."<br/>Error message: $errormsg"
                  ."<br/>Error code: $errornum";
        },
        "sv" => function() {
            return "Filen kunde inte sparas."
                  ."<br/>Felmeddelande: $errormsg"
                  ."<br/>Felkod: $errornum";
        },
    ),
    /*
    "" => array(
        "en" => function() { return ""; },
        "sv" => function() { return ""; },
    ),
    */
);

?>
