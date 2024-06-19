<?php

use MagicObject\SecretObject;
use MagicObject\Util\Database\PicoDatabaseUtil;

require_once __DIR__ . "/inc.app/app.php";
require_once __DIR__ . "/inc.app/navs.php";

?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>AppBuilder</title>
  <link rel="stylesheet" href="lib.assets/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="js/cm/lib/codemirror.css">
  <link rel="stylesheet" type="text/css" href="css/css.css">
  <script type="text/javascript" src="js/jquery/js/jquery-1.11.1.min.js"></script>
  <script type="text/javascript" src="lib.assets/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="js/base64.js"></script>
  <script src="js/cm/lib/codemirror.js"></script>
  <script src="js/cm/addon/mode/loadmode.js"></script>
  <script src="js/cm/addon/edit/matchbrackets.js"></script>
  <script src="js/cm/mode/meta.js"></script>
  <script src="js/cm/mode/sql/sql.js" type="text/javascript"></script>
  <script src="js/cm/mode/clike/clike.js" type="text/javascript"></script>
  <script src="js/cm/mode/php/php.js" type="text/javascript"></script>
  <script src="js/cm/mode/htmlmixed/htmlmixed.js"></script>
  <script src="js/cm/mode/xml/xml.js"></script>
  <script src="js/cm/mode/javascript/javascript.js"></script>
  <script src="js/cm/mode/css/css.js"></script>
  <script src="lib.assets/js/script.js"></script>
</head>

<body>
  <div class="all">
    <div class="tabs">
      <ul class="nav nav-tabs" id="myTab" role="tablist">
        <?php
        $navigators = $appNavs->getNavs();
        foreach ($navigators as $nav) {
        ?>
          <li class="nav-item" role="presentation">
            <button class="nav-link<?php echo $nav->getActive() ? ' active' : ''; ?>" id="<?php echo $nav->getKey(); ?>-tab" data-toggle="tab" data-target="#<?php echo $nav->getKey(); ?>" type="button" role="tab" aria-controls="config" aria-selected="true"><?php echo $nav->getCaption(); ?></button>
          </li>
        <?php
        }
        ?>

      </ul>
      <div class="tab-content" id="myTabContent">
        <?php
        $nav = $appNavs->item(0);
        $cfgDatabase = new SecretObject($builderConfig->getDatabase());
        $cfgSession = new SecretObject($builderConfig->getSessions());
        ?>
        <div id="<?php echo $nav->getKey(); ?>" class="tab-pane fade<?php echo $nav->getActive() ? ' show active' : ''; ?>" role="tabpanel" aria-labelledby="<?php echo $nav->getKey(); ?>-tab">

          <form name="formdatabase" id="formdatabase" method="post" action="" class="config-table">
            <h4>Database</h4>
            <table class="config-table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td>Driver</td>
                  <td>
                    <select class="form-control" name="database_driver" id="database_driver">
                      <option value="mysql" <?php echo $cfgDatabase->getDriver() == 'mysql' ? ' selected' : ''; ?>>MySQL</option>
                      <option value="mariadb" <?php echo $cfgDatabase->getDriver() == 'mariadb' ? ' selected' : ''; ?>>MariaDB</option>
                      <option value="postgresql" <?php echo $cfgDatabase->getDriver() == 'postgresql' ? ' selected' : ''; ?>>PostgreSQL</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>Host</td>
                  <td><input class="form-control" type="text" name="database_host" id="database_host" value="<?php echo $cfgDatabase->getHost(); ?>"></td>
                </tr>
                <tr>
                  <td>Port</td>
                  <td><input class="form-control" type="text" name="database_port" id="database_port" value="<?php echo $cfgDatabase->getPort(); ?>"></td>
                </tr>
                <tr>
                  <td>Username</td>
                  <td><input class="form-control" type="text" name="database_username" id="database_username" value="<?php echo $cfgDatabase->getUsername(); ?>"></td>
                </tr>
                <tr>
                  <td>Password</td>
                  <td><input class="form-control" name="database_password" type="password" id="database_password" value="<?php echo $cfgDatabase->getPassword(); ?>"></td>
                </tr>
                <tr>
                  <td>Name</td>
                  <td><input class="form-control" type="text" name="database_database_name" id="database_database_name" value="<?php echo $cfgDatabase->getName(); ?>"></td>
                </tr>
                <tr>
                  <td>Schema</td>
                  <td><input class="form-control" type="text" name="database_database_schema" id="database_database_schema" value="<?php echo $cfgDatabase->getSchema(); ?>"></td>
                </tr>
                <tr>
                  <td>Time Zone</td>
                  <td><input class="form-control" type="text" name="database_time_zone" id="database_time_zone" value="<?php echo $cfgDatabase->getTimeZone(); ?>"></td>
                </tr>
              </tbody>
            </table>

            <h4>Session</h4>
            <table class="config-table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td>Session Name</td>
                  <td><input class="form-control" type="text" name="sessions_name" id="sessions_name" value="<?php echo $cfgSession->getName(); ?>"></td>
                </tr>
                <tr>
                  <td>Session Life Time</td>
                  <td><input class="form-control" type="text" name="sessions_lifetime" id="sessions_lifetime" value="<?php echo $cfgSession->getMaxLifeTime(); ?>"></td>
                </tr>
                <tr>
                  <td>Session Save Handler</td>
                  <td>
                    <select class="form-control" name="sessions_save_handler" id="sessions_save_handler">
                      <option value="files" <?php echo $cfgSession->getSaveHandler() == 'files' ? ' selected' : ''; ?>>files</option>
                      <option value="redis" <?php echo $cfgSession->getSaveHandler() == 'redis' ? ' selected' : ''; ?>>redis</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>Session Save Path</td>
                  <td><input class="form-control" type="text" name="sessions_save_path" id="sessions_save_path" value="<?php echo $cfgSession->getSavePath(); ?>"></td>
                </tr>
              </tbody>
            </table>
            <table class="config-table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td>&nbsp;</td>
                  <td><input class="btn btn-success" type="button" name="save_config" id="save_config" value="Save Config"></td>
                </tr>
              </tbody>
            </table>
          </form>
        </div>

        <?php
        $nav = $appNavs->item(1);
        $cfgDatabase = new SecretObject($appConfig->getDatabase());
        $cfgSession = new SecretObject($appConfig->getSessions());
        ?>
        <div id="<?php echo $nav->getKey(); ?>" class="tab-pane fade<?php echo $nav->getActive() ? ' show active' : ''; ?>" role="tabpanel" aria-labelledby="<?php echo $nav->getKey(); ?>-tab">

          <form name="formdatabase" id="formdatabase" method="post" action="" class="config-table">
            <h4>Application</h4>
            <table class="config-table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td>Current Application</td>
                <td>
                  <select class="form-control" name="current_application" id="current_application">
                    <?php
                    $arr = $appList->valueArray();
                    foreach ($arr as $app) {
                      if ($app['id'] != null) {
                        if ($currentApplication != null && $currentApplication->getId() == $app['id']) {
                          $selected = ' selected';
                        } else {
                          $selected = '';
                        }
                    ?>
                        <option value="<?php echo $app['id']; ?>" <?php echo $selected; ?>><?php echo $app['name']; ?></option>
                    <?php
                      }
                    }
                    ?>
                  </select>

                </td>
              <tr>
                <td></td>
                <td>
                  <button type="button" class="btn btn-success" id="switch-application">
                    Apply
                  </button>

                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-create-application">
                    Create New
                  </button>
                </td>
              </tr>
              </tbody>
            </table>
            <h4>Database</h4>
            <table class="config-table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td>Driver</td>
                  <td>
                    <select class="form-control" name="database_driver" id="database_driver">
                      <option value="mysql" <?php echo $cfgDatabase->getDriver() == 'mysql' ? ' selected' : ''; ?>>MySQL</option>
                      <option value="mariadb" <?php echo $cfgDatabase->getDriver() == 'mariadb' ? ' selected' : ''; ?>>MariaDB</option>
                      <option value="postgresql" <?php echo $cfgDatabase->getDriver() == 'postgresql' ? ' selected' : ''; ?>>PostgreSQL</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>Host</td>
                  <td><input class="form-control" type="text" name="database_host" id="database_host" value="<?php echo $cfgDatabase->getHost(); ?>"></td>
                </tr>
                <tr>
                  <td>Port</td>
                  <td><input class="form-control" type="text" name="database_port" id="database_port" value="<?php echo $cfgDatabase->getPort(); ?>"></td>
                </tr>
                <tr>
                  <td>Username</td>
                  <td><input class="form-control" type="text" name="database_username" id="database_username" value="<?php echo $cfgDatabase->getUsername(); ?>"></td>
                </tr>
                <tr>
                  <td>Password</td>
                  <td><input class="form-control" name="database_password" type="password" id="database_password" value="<?php echo $cfgDatabase->getPassword(); ?>"></td>
                </tr>
                <tr>
                  <td>Name</td>
                  <td><input class="form-control" type="text" name="database_database_name" id="database_database_name" value="<?php echo $cfgDatabase->getDatabaseName(); ?>"></td>
                </tr>
                <tr>
                  <td>Schema</td>
                  <td><input class="form-control" type="text" name="database_database_schema" id="database_database_schema" value="<?php echo $cfgDatabase->getDatabaseSchema(); ?>"></td>
                </tr>
                <tr>
                  <td>Time Zone</td>
                  <td><input class="form-control" type="text" name="database_time_zone" id="database_time_zone" value="<?php echo $cfgDatabase->getTimeZone(); ?>"></td>
                </tr>
              </tbody>
            </table>

            <h4>Session</h4>
            <table class="config-table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td>Session Name</td>
                  <td><input class="form-control" type="text" name="sessions_name" id="sessions_name" value="<?php echo $cfgSession->getName(); ?>"></td>
                </tr>
                <tr>
                  <td>Session Life Time</td>
                  <td><input class="form-control" type="text" name="sessions_lifetime" id="sessions_lifetime" value="<?php echo $cfgSession->getMaxLifeTime(); ?>"></td>
                </tr>
                <tr>
                  <td>Session Save Handler</td>
                  <td>
                    <select class="form-control" name="sessions_save_handler" id="sessions_save_handler">
                      <option value="files" <?php echo $cfgSession->getSaveHandler() == 'files' ? ' selected' : ''; ?>>files</option>
                      <option value="redis" <?php echo $cfgSession->getSaveHandler() == 'redis' ? ' selected' : ''; ?>>redis</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>Session Save Path</td>
                  <td><input class="form-control" type="text" name="sessions_save_path" id="sessions_save_path" value="<?php echo $cfgSession->getSavePath(); ?>"></td>
                </tr>
              </tbody>
            </table>

            <h4>Reserved Columns</h4>
            <table class="config-table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <?php
                $entityConstant = new SecretObject($appConfig->getEntityInfo());
                if (empty($entityConstant->valueArray())) {
                  $entityConstant = new SecretObject($builderConfig->getEntityInfo());
                }
                $arr = $entityConstant->valueArray(true);

                if (!empty($arr)) {
                  foreach ($arr as $key => $value) {
                ?>
                    <tr>
                      <td><?php echo $key ?></td>
                      <td><input class="form-control" type="text" name="entity_info_<?php echo $key ?>" value="<?php echo $value; ?>"></td>
                    </tr>
                <?php
                  }
                }
                ?>


              </tbody>
            </table>

            <table class="config-table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td>&nbsp;</td>
                  <td><input class="btn btn-success" type="button" name="save_application_config" id="save_application_config" value="Save Config"></td>
                </tr>
              </tbody>
            </table>
          </form>
        </div>

        <?php
        $nav = $appNavs->item(2);
        ?>
        <div id="<?php echo $nav->getKey(); ?>" class="tab-pane fade<?php echo $nav->getActive() ? ' show active' : ''; ?>" role="tabpanel" aria-labelledby="<?php echo $nav->getKey(); ?>-tab">
          <h4>Entity</h4>
          <form name="formdatabase" id="formdatabase" method="post" action="" class="config-table">
            <table class="config-table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td>Table</td>
                  <td><select class="form-control" name="source_table">

                    </select>
                  </td>
                </tr>

                <tr>
                  <td>&nbsp;</td>
                  <td>
                    <input class="btn btn-primary" type="button" name="load_table" id="load_table" value="Load Table">

                  </td>
                </tr>

                <tr>
                  <td>Master Entity Name</td>
                  <td><input class="form-control" type="text" name="entity_master_name" id="entity_master_name"></td>
                </tr>
                <tr>
                  <td>Master Primary Key</td>
                  <td><input class="form-control" type="text" name="primary_key_master" id="primary_key_master"></td>
                </tr>
                <tr>
                  <td>Approval Table Name</td>
                  <td><input class="form-control" type="text" name="table_approval_name" id="table_approval_name"></td>
                </tr>
                <tr>
                  <td>Approval Primary Key</td>
                  <td><input class="form-control" type="text" name="primary_key_approval" id="primary_key_approval"></td>
                </tr>
                <tr>
                  <td>Approval Entity Name</td>
                  <td><input class="form-control" type="text" name="entity_approval_name" id="entity_approval_name"></td>
                </tr>
                <tr>
                  <td>Trash Table Name</td>
                  <td><input class="form-control" type="text" name="table_trash_name" id="table_trash_name"></td>
                </tr>
                <tr>
                  <td>Trash Primary Key</td>
                  <td><input class="form-control" type="text" name="primary_key_trash" id="primary_key_trash"></td>
                </tr>
                <tr>
                  <td>Trash Entity Name</td>
                  <td><input class="form-control" type="text" name="entity_trash_name" id="entity_trash_name"></td>
                </tr>
              </tbody>
            </table>
            <h4>Module</h4>
            <table class="config-table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td>File Name</td>
                  <td><input class="form-control" type="text" name="module_file" id="module_file"></td>
                </tr>
                <tr>
                  <td>Module Name</td>
                  <td><input class="form-control" type="text" name="module_name" id="module_name"></td>
                </tr>
                <tr>
                  <td>Add To Database</td>
                  <td><label for=""><input type="checkbox" name="module_add_to_database"> Yes</label></td>
                </tr>
                <tr>
                  <td>Special Access</td>
                  <td><label for=""><input type="checkbox" name="module_special_access"> Yes</label></td>
                </tr>
                <tr>
                  <td>Add Menu</td>
                  <td><label for=""><input type="checkbox" name="module_ass_menu"> Yes</label></td>
                </tr>
              </tbody>
            </table>

            <table class="config-table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td></td>
                  <td><input class="btn btn-success" type="button" name="load_column" id="load_column" value="Load Column"></td>
                </tr>

              </tbody>
            </table>


          </form>
        </div>
        <?php
        $nav = $appNavs->item(3);
        ?>
        <div id="<?php echo $nav->getKey(); ?>" class="tab-pane fade<?php echo $nav->getActive() ? ' show active' : ''; ?>" role="tabpanel" aria-labelledby="<?php echo $nav->getKey(); ?>-tab">
          <div class="define-wrapper">
            <form name="formgenerator" id="formgenerator" method="post" action="">
              <table width="100%" border="1" cellspacing="0" cellpadding="0" class="main-table">
                <thead>
                  <tr>
                    <td rowspan="2" align="center">Field</td>
                    <td rowspan="2" align="center">Caption</td>
                    <td rowspan="2" align="center" width="32" title="Insert">I</td>
                    <td rowspan="2" align="center" width="32" title="Update">U</td>
                    <td rowspan="2" align="center" width="32" title="Detail">D</td>
                    <td rowspan="2" align="center" width="32" title="List">L</td>
                    <td rowspan="2" align="center" width="32" title="Key">K</td>
                    <td rowspan="2" align="center" width="32" title="Required">R</td>
                    <td colspan="5" align="center">Element Type</td>
                    <td colspan="3" align="center" width="60">Search</td>
                    <td rowspan="2" align="center" width="100">Data Type</td>
                    <td rowspan="2" align="center" width="180">Filter Type</td>
                  </tr>
                  <tr>
                    <td align="center" width="32" title="Input Text">TE</td>
                    <td align="center" width="32" title="Textarea">TA</td>
                    <td align="center" width="32" title="Checkbox">CB</td>
                    <td align="center" width="32" title="Select">SE</td>
                    <td align="center" width="74" title="Select">Source</td>
                    <td align="center" width="30">TE</td>
                    <td align="center" width="30">SE</td>
                    <td align="center" width="74" title="Select">Source</td>
                  </tr>
                </thead>

                <tbody>
                </tbody>
              </table>
              <div class="button-area">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-module-features">
                  Module Features
                </button>
                <input class="btn btn-success" type="button" name="generate-script" id="generate-script" value="Generate Script"> &nbsp;            
                
                <div class="modal fade" id="modal-module-features" tabindex="-1" aria-labelledby="modal_features_label" aria-hidden="true">
                  <div class="modal-dialog modal-md">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Module Feature</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <table class="config-table" width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tbody>
                            <tr>
                              <td>Activate/Decativate</td>
                              <td><label><input type="checkbox" name="actiavte_deactivate" id="actiavte_deactivate" value="1"> Activate/Decativate</label> &nbsp;</td>
                            </tr>
                            <tr>
                              <td>Sort Order</td>
                              <td><label><input type="checkbox" name="manualsortorder" id="manualsortorder" value="1"> Sort Order</label> &nbsp;</td>
                            </tr>
                            <tr>
                              <td>Approval</td>
                              <td><label><input type="checkbox" name="with_approval" id="with_approval" value="1"> Approval</label> &nbsp;</td>
                            </tr>
                            <tr>
                              <td>Approval Note</td>
                              <td><label><input type="checkbox" name="with_approval_note" id="with_approval_note" value="1"> Approval Note</label> &nbsp;</td>
                            </tr>
                            <tr>
                              <td>Trash</td>
                              <td><label><input type="checkbox" name="with_trash" id="with_trash" value="1"> Trash</label> &nbsp;</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                      </div>
                    </div>
                  </div>
                </div>
                
              </div>
            </form>
          </div>
        </div>
        <?php
        $nav = $appNavs->item(4);
        ?>
        <div id="<?php echo $nav->getKey(); ?>" class="tab-pane fade<?php echo $nav->getActive() ? ' show active' : ''; ?>" role="tabpanel" aria-labelledby="generated-file-tab">
        </div>
        <div id="generated-file" class="tab-pane fade" role="tabpanel" aria-labelledby="generated-file-tab">
          <div class="code-area">
            <textarea class="text-code" class="text-code-php" spellcheck="false"></textarea>
          </div>
        </div>
        <div id="generated-query" class="tab-pane fade" role="tabpanel" aria-labelledby="generated-query-tab">
          <div class="code-area">
            <textarea class="text-code" class="sql-from-server" spellcheck="false"></textarea>
          </div>
        </div>
        <div id="generated-entity" class="tab-pane fade" role="tabpanel" aria-labelledby="generated-entity-tab">
        
        <div class="generated-entity-container">
          <div class="row">
            <div class="col col-2">
              <div class="column-title"><h4>Entities</h4></div>
              <div class="column-body">
                <div class="entity-list"></div>
              </div>
            </div>
            <div class="col col-10">
              <div class="column-title"><h4>Query</h4></div>
              <div class="column-body">
                <div class="entity-query"></div>
              </div>
            </div>
          </div>
        </div>
        
        </div>
        <div id="docs" class="tab-pane fade" role="tabpanel" aria-labelledby="docs-tab">
          <div class="desc">
            <h4>Column Description</h4>
            <ol>
              <li><strong>Field</strong><br />
                Field is the column name of the table
              </li>
              <li><strong>Caption</strong><br />
                Caption is label of associated column
              </li>
              <li><strong>I</strong><br />
                If the field will included on &quot;insert&quot; section, please check this
              </li>
              <li><strong>U</strong><br />
                If the field will included on &quot;update&quot; section, please check this </li>
              <li><strong>D</strong><br />
                If the field will included on &quot;detail&quot; section, please check this </li>
              <li><strong>L</strong><br />
                If the field will included on &quot;list&quot; section, please check this </li>
              <li><strong>K</strong><br />
                Is the field is primary key of the table, please check this. Each modul must have one key for control data
              </li>
              <li><strong>R</strong><br />
                If input is mandatory to filed, please check this
              </li>
              <li><strong>TE</strong><br />
                Input type is &lt;input type=&quot;text&quot;&gt;, &lt;input type=&quot;email&quot;&gt;, &lt;input type=&quot;tel&quot;&gt;, &lt;input type=&quot;password&quot;&gt;, &lt;input type=&quot;number&quot;&gt;, or &lt;input type=&quot;number&quot; step=&quot;any&quot;&gt; according to data type</li>
              <li><strong>TA</strong><br />
                Input type is &lt;textarea&gt;&lt;/textarea&gt;
              </li>
              <li><strong>SE</strong><br />
                Input type is &lt;select&gt;&lt;option value&quot;&quot;&gt;&lt;/option&gt;&lt;/select&gt;
              </li>
              <li><strong>CB</strong><br />
                Input type is &lt;input type=&quot;checkbox&quot;&gt;
              </li>
              <li><strong>Data Type</strong><br />
                Data type for &lt;input&gt;
              </li>
              <li><strong>Filter Type</strong><br />
                Filter type for input sent to server</li>
            </ol>
            <p>&lt;input type=&quot;date&quot;&gt;, &lt;input type=&quot;datetime&quot;&gt;, &lt;input type=&quot;time&quot;&gt; and &lt;input type=&quot;color&quot;&gt; are not applied on this generator because not all browsers support these input type. You can use JavaScript library for its.</p>
          </div>
        </div>
      </div>
    </div>
  </div>


  <div class="modal fade" id="modal-create-application" tabindex="-1" aria-labelledby="application_modal_label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Create New Application</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="">
            <?php
            $appName = "YourAppName";
            $appAuthor = "YourName";
            $appId = PicoDatabaseUtil::uuid();
            $appBaseDir = dirname(__DIR__) . "/$appId";
            $appBaseDir = str_replace("/", DIRECTORY_SEPARATOR, $appBaseDir);
            $appBaseDir = str_replace("\\", DIRECTORY_SEPARATOR, $appBaseDir);
            ?>
            <table class="config-table" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td>Application Name</td>
                  <td><input class="form-control" type="text" name="application_name" value="<?php echo $appName; ?>"></td>
                </tr>
                <tr>
                  <td>Application ID</td>
                  <td><input class="form-control" type="text" name="application_id" value="<?php echo $appId ?>"></td>
                </tr>
                <tr>
                  <td>Application Directory</td>
                  <td><input class="form-control" type="text" name="application_directory" data-base-directory="<?php echo $appBaseDir; ?>" value="<?php echo $appBaseDir; ?>"></td>
                </tr>
                <tr>
                  <td>Author</td>
                  <td><input class="form-control" type="text" name="application_author" value="<?php echo $appAuthor; ?>"></td>
                </tr>
              </tbody>
            </table>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary">Save Changes</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal-create-reference-data" tabindex="-1" aria-labelledby="reference_data_modal_label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Create Data Reference</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="copy-from-other">Copy From Filter</button>
          <button type="button" class="btn btn-primary" id="load-from-cache">Load</button>      
          <button type="button" class="btn btn-primary" id="save-to-cache">Save</button>
          <button type="button" class="btn btn-success" id="apply-reference">OK</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</body>

</html>