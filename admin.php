<?php
    include('session.php');
    if ($login_role == 1) {
        header("location:index.php");
        die();
    }

    $result = '';
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        foreach ($_POST as $key => $value) {
            //do something
            $sql = "UPDATE fields SET value='$value' WHERE type=0 AND field_id='$key'";
            if (mysqli_query($db, $sql) !== TRUE) {
                echo "An error occured";
            }
        }
        echo "Successfully updated";
    }
    $field_list = mysqli_query($db, "SELECT a.*, b.formula FROM fields a LEFT JOIN formulas b ON a.field_id = b.field_id ORDER BY field_id DESC");
    $headertext = mysqli_query($db, "SELECT * FROM headercontent");
   
    if($headertext)
    { 
        $headertext = mysqli_fetch_all($headertext, MYSQLI_ASSOC);
    }

?>

<!DOCTYPE html>
<!-- saved from url=(0033)http://demo1.viwiz.net/simulator/ -->
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Simulator</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/font-awesome/css/font-awesome.min.css">
</head>


<body>
    <section>
        <a href="./logout.php">Logout (<?php echo $login_user;?>)</a>
    </section>

    <section id="header" style="vertical-align:middle;">
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <?php if($headertext) {?>
                <h1><?php echo $headertext[0]['content']; ?></h1>
                <?php } else{ ?>
                <h1><?php echo "Set Header!"; } ?></h1>
            </div>
            <div class="col-md-2">
                <a class="btn btn-success getheader" style="margin-top:18%;" data-id = "<?php echo $headertext[0]['id'];?>">
                    <i class="fa fa-edit"></i> Edit Header
                </a>
            </div>
        </div>
    </section>

    <div class="portlet light">
        <table class="table table-striped table-bordered table-hover table-checkable">
            <tbody>
                <tr>
                    <?php
                    foreach($headertext as $key=>$value) { 
                        if($key == 0)
                            continue;?>                  
                            <td class="col-sm-2">
                                <h2 class="title"><?php echo $value['content'];?></h2>
                            </td>
                    <?php }?> 
                </tr> 
                <tr>
                    <?php
                    foreach($headertext as $key=>$value) { 
                        if($key == 0)
                            continue;?>                  
                            <td class="col-sm-2">
                                <a class="btn btn-success getheader" data-id = "<?php echo $value['id'];?>" >                            
                                    <i class="fa fa-edit"></i> Edit Category
                                </a>
                            </td>
                    <?php }?> 
                </tr>      
            </tbody>
        </table>
    </div>

    <section>
        <div class="portlet light bordered">
            <div class="portlet-body">
                <div class="btn-group">
                    <a class="btn sbold green add_new"> Add New <i class="fa fa-plus"></i></a>
                </div>
                <table class="table table-striped table-bordered table-hover order-column"
                    id="field_list">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Field ID</th>
                            <th>Name</th>
                            <th>Value</th>
                            <th>Unit</th>
                            <th>Type</th>
                            <th>Editable</th>
                            <th>Description</th>
                            <th>Formula</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            while ($row = mysqli_fetch_array($field_list, MYSQLI_ASSOC)) {
                        ?>
                        <tr class="odd gradeX">
                            <td>
                                <?php 
                                    $id = $row['header_id'];
                                    $query = mysqli_query($db,'SELECT content FROM headercontent WHERE id = ' . $id);
                                    $result = mysqli_fetch_array($query, MYSQLI_ASSOC);
                                    echo $result['content'];
                                ?>
                            </td>
                            <td><?php echo $row['field_id'];?></td>
                            <td><?php echo $row['field_label'];?></td>
                            <td><?php echo $row['value'];?></td>
                            <td><?php echo $row['unit'];?></td>
                            <td><?php echo $row['type'] ? 'Dropdown' : 'Input';?></td>
                            <td><?php echo $row['editable'] ? 'YES' : 'NO';?></td>
                            <td><?php echo $row['description'];?></td>
                            <td><?php echo $row['formula'];?></td>
                            <td>
                                <a class="btn btn-success editformula"attr_id = "<?php echo $row['field_id'];?>">
                                    <i class="fa fa-edit"></i> Edit Formula
                                </a>
                                <a class="btn btn-primary medit" class="edit_dialog" attr_id = "<?php echo $row['id'];?>">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <?php if ($row['header_id'] != 100) { ?>
                                <a class="btn grey delete" attr_id="<?php echo $row['id'];?>" data-toggle="modal" href="#mdelete">
                                    <i class="fa fa-trash"></i> Delete
                                </a>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

   <!-- Edit start -->
    <div class="modal fade" id="header1" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Edit Field</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12"> <input type="text" class="form-control headertext" name=""> </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                    <button type="button" class="btn green saveheader">Save</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit end -->

<!-- Add New start -->
    <div class="modal fade" id="formula" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Edit Formula</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="text" class="formula_desc form-control"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                    <button type="button" class="btn green save_formula">Save</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Add New end -->

    <!-- Edit start -->
    <div class="modal fade" id="medit" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Edit Field</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-2" style="margin-top: 7px"> Category</div>
                        <div class="col-md-5"> 
                            <select class="form-control category_id">
                            <?php foreach( $headertext as $key=>$value) {
                                if($key == 0) continue;?>
                                <option value="<?php echo $key;?>"><?php echo $value['content']; ?></option>
                            <?php } ?>
                            </select>  
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-2" style="margin-top: 7px"> Field Id</div>
                        <div class="col-md-5"> <input type="text" class="form-control field_id" name=""> </div>
                        <div class="col-md-3"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-2" style="margin-top: 7px"> Name</div>
                        <div class="col-md-5"> <input type="text" class="form-control name_edit" name=""> </div>
                        <div class="col-md-3"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-2" style="margin-top: 7px"> Value</div>
                        <div class="col-md-5">
                            <div class="row" style="margin-bottom: 0px;padding: 0px;">
                                <div class="col-md-8">
                                    <input type="text" class="form-control value_edit" name="">
                                </div>
                                <div class="col-md-4 add_value_box" style="display: none;">
                                    <button class="btn sbold blue add_value">Add Value <i
                                            class="fa fa-plus"></i></button>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 0px;padding: 0px;">
                                <div class="col-md-12 value_container" style="display:none;margin-top: 10px;">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-2" style="margin-top: 7px"> Unit</div>
                        <div class="col-md-5"> <input type="text" class="form-control unit_edit" name=""> </div>
                        <div class="col-md-3"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-2" style="margin-top: 7px"> Type</div>
                        <div class="col-md-5"> <select class="form-control fieldtype">
                                <option value="0">Input</option>
                                <option value="1">DropDown</option>
                            </select> </div>
                        <div class="col-md-3"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-2" style="margin-top: 7px"> Editable</div>
                        <div class="col-md-5"> <select class="form-control fieldeditable">
                                <option value="1">YES</option>
                                <option value="0">NO</option>
                            </select> </div>
                        <div class="col-md-3"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-2" style="margin-top: 7px"> Description</div>
                        <div class="col-md-8">
                            <!-- <input type="text" class="form-control description_edit" name=""> -->
                            <textarea rows="3" class="form-control description_edit"></textarea>
                        </div>
                        <!-- <div class="col-md-3"></div> -->
                    </div>
                    <div class="row" style="color: #777">
                        <p>NOTE: To add image or video in your description, please input html code like the followings.</p>
                        <p>
                            Title: &lt;h1&gt;TITLE_HERE&lt;/h1&gt;<br/>
                            Image: &lt;img src="IMAGE_URL_HERE" width="300"&gt;<br/>
                            Youtube video: &lt;iframe src="VIDEO_URL_HERE" width="300" height="250"&gt;&lt;/iframe&gt;
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                    <button type="button" class="btn green change">Save</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit end -->
    <!-- Delete start -->
    <div class="modal fade" id="mdelete" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Delete</h4>
                </div>
                <div class="modal-body" style="text-align: center"> Are You Sure? </div>
                <div class="modal-footer">
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                    <button type="button" class="btn green delete_save" attr_id="">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Delete end -->
</body>

<link href="./bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="./bootstrap/css/datatables.min.css" rel="stylesheet" type="text/css" />
<link href="./bootstrap/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
<link href="./bootstrap/css/plugins.min.css" rel="stylesheet" type="text/css" />
<style type="text/css">
    th, td {
        text-align: center;
    }

    .item {
        background-color: black;
        color: white;
        padding: 5px;
        border-radius: 25px !important;
    }

    .delete_value:hover {
        cursor: pointer;
        color: red;
    }

    h1 {
        text-align: center;
    }
</style>


<script src="./bootstrap/js/jquery.min.js" type="text/javascript"></script>
<script src="./bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="./bootstrap/js/datatables.min.js" type="text/javascript"></script>
<script src="./bootstrap/js/datatables.bootstrap.js" type="text/javascript"></script>
<script src="./bootstrap/js/app.min.js" type="text/javascript"></script>
<script src="./bootstrap/js/table-datatables-managed.min.js" type="text/javascript"></script>
<script src="./js/script.js" type="text/javascript"></script>

<script>
    $(function () {
        $('#field_list').DataTable({
            aaSorting: [[0, 'desc']]
        });
    })
</script>