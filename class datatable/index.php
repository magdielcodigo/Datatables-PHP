<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/dataTables.bootstrap5.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap5.min.css">
</head>
<body>
    
    <div class="container">
        <table id="example" class="table table-striped table-bordered dt-responsive nowrap w-100">
            <thead>
                <th>#</th>
                <th>NOMBRE</th>
                <th>EDAD</th>
                <th>TELEFONO</th>
                <th>OPTIONS</th>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js">
    </script>
    <script src="https://cdn.datatables.net/1.11.2/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.2/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/responsive.bootstrap5.min.js"></script>
    <script>
        let example = null;
        var datttt = '';
        $(()=>{
            loadTable()
        })
        const loadTable = ()=>{
            if(example){
                example.ajax.reload()
            }else{
                example = $("#example").DataTable({
                    paging: true,
                    searching: true,
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    ajax:{
                        url:'controller/datatablesController.php',
                        method:'POST',
                        dataSrc: 'data',
                        error:function(err){
                            console.log(err)
                        }
                    },
                    drawCallback: function(settings) {
                        datttt = settings.json.draw;
                    },
                    columns:[
                        {
                            data:'id'
                        },
                        {
                            data:'nombre'
                        },
                        {
                            data:'edad'
                        },
                        {
                            data:'telefono'
                        },
                        {
                            data:null,
                            render:function(data,type,row){
                                console.log(datttt);
                                if(datttt == 1){
                                    return 'uno entro'
                                }
                                return 'holas'
                            }
                        }
                    ]
                })
            }
        }
    </script>
</body>
</html>