<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta content="" name="description" />
    <meta content="webthemez" name="author" />
    <title>青春521后台管理</title>
    <!-- Bootstrap Styles-->
    <link href="/static/assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FontAwesome Styles-->
    <link href="/static/assets/css/font-awesome.css" rel="stylesheet" />
    <!-- Morris Chart Styles-->

    <!-- Custom Styles-->
    <link href="/static/assets/css/custom-styles.css" rel="stylesheet" />
    <!-- Google Fonts-->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <!-- TABLE STYLES-->
    <link href="/static/assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
</head>
<body>
<div id="wrapper">
    <nav class="navbar navbar-default top-navbar" role="navigation">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse"
                    data-target=".sidebar-collapse">
                <span class="sr-only">Toggle navigation</span> <span
                        class="icon-bar"></span> <span class="icon-bar"></span> <span
                        class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"><strong><i
                            class="icon fa fa-plane"></i> 选项</strong></a>

            <div id="sideNav" href="">
                <i class="fa fa-bars icon"></i>
            </div>
        </div>
        <!-- 右边的bar栏目 -->
        <ul class="nav navbar-top-links navbar-right">
            <li class="dropdown"><a class="dropdown-toggle"
                                    data-toggle="dropdown" href="#" aria-expanded="false"> <i
                            class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    <li><a href="/backstage/logout"><i class="fa fa-sign-out fa-fw"></i> 注销</a></li>
                </ul> <!-- /.dropdown-user --></li>
            <!-- /.dropdown -->
        </ul>
    </nav>


    <!--/. NAV TOP  -->
    <nav class="navbar-default navbar-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav" id="main-menu">
                <li><a href="/backstage"><i class="fa fa-dashboard"></i> 控制台</a></li>
                <li><a class="active-menu" href="/backstage/approval"><i class="fa fa-qrcode"></i> 外校注册审批</a></li>
            </ul>

        </div>

    </nav>
    <!-- /. NAV SIDE  -->

    <div id="page-wrapper">
        <div class="header">
            <h1 class="page-header">外校注册审批<small>Welcome {{$admin_name}}</small></h1>
            <ol class="breadcrumb">
                <li class=""><a href="/backstage"> 控制台</a></li>
                <li class="active">外校注册审批</li>
            </ol>

        </div>

        <div id="page-inner">

            <div class="row">
                <div class="col-md-12">
                    <!-- Advanced Tables -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            外校注册列表
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                    <tr>
                                        <th>学校</th>
                                        <th>学生证正面照</th>
                                        <th>一卡通正面照</th>
                                        <th>操作</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($list as $list)
                                        <tr class="odd gradeX">
                                            <td>{{$list->school}}</td>
                                            <td><img src="/uploads/apply/{{$list->xsz}}" width="400" height="300"></td>
                                            <td><img src="/uploads/apply/{{$list->ykt}}" width="400" height="300"></td></td>
                                            <td class="center">
                                                <a href="{{ url('/backstage/applypass', ['id' => $list->id]) }}"><button type="button" class="btn btn-success">通过申请</button></a>

                                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#{{$list->id}}" style="margin-bottom: 0px;">
                                                    拒绝申请
                                                </button>
                                                <div class="modal fade" id="{{$list->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                <h4 class="modal-title" id="myModalLabe2">拒绝申请</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-8 col-md-offset-2">
                                                                        <form role="form" method="post" enctype="multipart/form-data" action="{{ url('/backstage/applydispass', ['id' => $list->id]) }}">
                                                                            <div class="form-group">
                                                                                <label>拒绝原因</label><small style="">最多300字</small><br/>
                                                                                <textarea class="form-control" rows="3"  data-val="" name="reason" maxlength="300" style="width: 100%"></textarea>
                                                                            </div>
                                                                            <label></label>
                                                                            <input type="hidden" name="__token__" value="{$Request.token}" />
                                                                            <input type="submit" id="submit" class="btn btn-success" value=" 确定 " style="width: 100%">
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                    <!--End Advanced Tables -->
                </div>
            </div>
            <!-- /. ROW  -->
        </div>
        <footer>

        </footer>
    </div>
    <!-- /. PAGE INNER  -->
</div>
<!-- /. PAGE WRAPPER  -->
<!-- /. WRAPPER  -->
<!-- JS Scripts-->
<!-- jQuery Js -->
<script src="/static/assets/js/jquery-1.10.2.js"></script>
<!-- Bootstrap Js -->
<script src="/static/assets/js/bootstrap.min.js"></script>
<!-- Metis Menu Js -->
<script src="/static/assets/js/jquery.metisMenu.js"></script>
<!-- DATA TABLE SCRIPTS -->
<script src="/static/assets/js/dataTables/jquery.dataTables.js"></script>
<script src="/static/assets/js/dataTables/dataTables.bootstrap.js"></script>
<script>
    $(document).ready(function () {
        $('#dataTables-example').dataTable();
    });
</script>
<!-- Custom Js -->
<script src="/static/assets/js/custom-scripts.js"></script>


</body>
</html>
