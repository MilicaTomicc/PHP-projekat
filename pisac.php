<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css" rel=" stylesheet">

    <title>Pisci</title>
</head>

<body>
    <?php
        include "header.php";
    ?>

    <div class="container mt-2">
        <h1 class="text-center">
            Pisci
        </h1>
        <div class=" mt-2">
            <input placeholder="Pretrazi..." type="text" id="pretraga" class="form-control">
        </div>
        <div class="row">
            <div class="col-5">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ime</th>
                            <th>Prezime</th>
                            <th>Obrisi</th>
                        </tr>
                    </thead>
                    <tbody id="tabela">

                    </tbody>
                </table>
            </div>
            <div class="col-1">

            </div>
            <div class="col-6">
                <form class="mt-2" id='forma'>
                    <h3>
                        Kreiraj pisca
                    </h3>
                    <div class="form-group">
                        <label for="ime">Ime</label>
                        <input required type="text" class="form-control" id="ime" placeholder="Ime">
                    </div>
                    <div class="form-group">
                        <label for="prezime">Prezime</label>
                        <input required type="text" class="form-control" id="prezime" placeholder="Prezime">
                    </div>
                    <button type="submit" class="btn btn-primary form-control">Sacuvaj</button>
                </form>

            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script>
        let pisci = [];
        let pretraga = '';

        $(function () {
            ucitajPisce();
            $('#pretraga').change(() => {
                setPretraga($('#pretraga').val());
            });

            $('#forma').submit(e => {
                e.preventDefault();
                const ime = $('#ime').val();
                const prezime = $('#prezime').val();
                $.post('./server/pisac.php', {
                    prezime,
                    ime,
                    metoda: 'create'
                }).then((val) => {
                    val = JSON.parse(val);
                    if (!val.status) {
                        alert(val.error);
                        return;
                    }
                    ucitajPisce();
                })
            })
        })

        function setPretraga(val) {
            pretraga = val;

            popuniTabelu(pisci.filter(element => {
                return element.ime.toLowerCase().includes(pretraga.toLowerCase()) || element.prezime.toLowerCase().includes(pretraga.toLowerCase());
            }))
        }
        function ucitajPisce() {
            $.getJSON('./server/pisac.php?metoda=read').then(val => {
                if (!val.status) {
                    alert(val.error);
                    return;
                }
                setPisci(val.data);
            });
        }
        function setPisci(val) {
            pisci = val;
            popuniTabelu(pisci);
        }
        function popuniTabelu(p) {
            $('#tabela').html('');
            for (let pisac of p) {
                $('#tabela').append(`
                    <tr>
                        <td>${pisac.id}</td>
                        <td>${pisac.ime}</td>
                        <td>${pisac.prezime}</td>
                        <td> 
                            <button class='btn btn-danger' onClick="obrisi(${pisac.id})">Obrisi</button>    
                        </td>
                    </tr>
                `)
            }
        }
        function obrisi(id) {
            $.post("./server/pisac.php", {
                metoda: 'delete',
                id
            }).then(val => {
                val = JSON.parse(val);
                if (!val.status) {
                    alert(val.error);
                    return;
                }
                ucitajPisce();
            });
        }
    </script>
</body>

</html>