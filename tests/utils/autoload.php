<?php

//Import Core Classes
require_once '../lib/Cake/bootstrap.php';
foreach (glob("../lib/Cake/Core/*.php") as $filename)  require_once($filename);

//Import Controllers
foreach (glob("../app/Controller/*.php") as $filename) require_once($filename);

//Import Component
foreach (glob("../app/Controller/Component/*.php") as $filename) require_once($filename);

//Import Console
foreach (glob("../app/Console/Command/*.php") as $filename) require_once($filename);

//Import Models
foreach (glob("../app/Model/*.php") as $filename)   require_once($filename);
foreach (glob("../app/Model/*/*.php") as $filename) require_once($filename);

//Import Events
foreach (glob("../app/Event/*.php") as $filename) require_once($filename);