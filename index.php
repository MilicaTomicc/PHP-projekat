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
            Knjige
        </h1>
        <div class=" mt-2 row mb-3">
            <div class="col-2">
                <select class="form-control" id="pisacPretraga">
                    <option value="0">Izaberite pisca</option>
                </select>
            </div>
            <div class="col-8">
                <input placeholder="Pretrazi..." type="text" id="pretraga" class="form-control">
            </div>
            <div class="col-2">
                <select class="form-control" id="zanrPretraga">
                    <option value="0">Izaberite zanr</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-8">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Naslov</th>
                            <th>ISBN</th>
                            <th>Broj strana</th>
                            <th>Pisac</th>
                            <th>Zanr</th>
                            <th>Akcije</th>
                        </tr>
                    </thead>
                    <tbody id="tabela">

                    </tbody>
                </table>
            </div>

            <div class="col-4">
                <form class="mt-2" id='forma'>
                    <h3 class="formaKnjiga">
                        Kreiraj knjigu
                    </h3>
                    <div class="form-group">
                        <label for="naslov">Naslov</label>
                        <input required type="text" class="form-control" id="naslov" placeholder="Naslov">
                    </div>
                    <div class="form-group">
                        <label for="isbn">ISBN</label>
                        <input required type="text" class="form-control" id="isbn" placeholder="ISBN">
                    </div>
                    <div class="form-group">
                        <label for="brojStrana">Broj strana</label>
                        <input required type="number" min='1' class="form-control" id="brojStrana"
                            placeholder="Broj strana">
                    </div>
                    <div class="form-group">
                        <label for="zanr">Zanr</label>
                        <select required class="form-control" id="zanr"></select>
                    </div>
                    <div class="form-group">
                        <label for="pisac">Pisac</label>
                        <select required class="form-control" id="pisac"></select>
                    </div>
                    <div class="form-group">
                        <label for="opis">Opis</label>
                        <textarea required class="form-control" id="opis"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary form-control">Sacuvaj</button>
                    <button id='vratiSe' hidden class=" mt-2 btn btn-secondary form-control">Vrati se</button>
                </form>

            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script>
        let knjige = [];
        let selId = 0;

        $(function () {
            ucitajKnjige();
            ucitajPisce();
            ucitajZanrove();
            $('#pretraga').change(() => {
                popuniTabelu();
            });
            $('#zanrPretraga').change(() => {
                popuniTabelu();
            })
            $('#pisacPretraga').change(() => {
                popuniTabelu();
            })
            $('#vratiSe').click(() => {
                otvoriIzmenu(0);
            })
            $('#forma').submit(e => {
                e.preventDefault();
                const naslov = $('#naslov').val();
                const opis = $('#opis').val();
                const brojStrana = $('#brojStrana').val();
                const isbn = $('#isbn').val();
                const zanr = $('#zanr').val();
                const pisac = $('#pisac').val();
                $.post('./server/knjiga.php', {
                    naslov,
                    opis,
                    brojStrana,
                    isbn,
                    zanr,
                    pisac,
                    id: selId,
                    metoda: selId > 0 ? 'update' : 'create'
                }).then((val) => {
                    val = JSON.parse(val);
                    if (!val.status) {
                        alert(val.error);
                        return;
                    }
                    ucitajKnjige();
                })
            })
        })


        function ucitajPisce() {
            $.getJSON('./server/pisac.php?metoda=read').then(val => {
                if (!val.status) {
                    alert(val.error);
                    return;
                }
                popuniComboBox('pisacPretraga', val.data, e => e.id, e => (e.ime + ' ' + e.prezime));
                popuniComboBox('pisac', val.data, e => e.id, e => (e.ime + ' ' + e.prezime))
            })
        }
        function ucitajZanrove() {
            $.getJSON('./server/zanr.php').then(val => {
                if (!val.status) {
                    alert(val.error);
                    return;
                }
                popuniComboBox('zanrPretraga', val.data, e => e.id, e => e.naziv);
                popuniComboBox('zanr', val.data, e => e.id, e => e.naziv)
            })
        }
        function ucitajKnjige() {
            $.getJSON('./server/knjiga.php?metoda=read').then(val => {
                if (!val.status) {
                    alert(val.error);
                    return;
                }
                knjige = val.data;
                popuniTabelu();
            });
        }
        function popuniTabelu() {
            const pretraga = $('#pretraga').val();
            const zanr = Number($('#zanrPretraga').val());
            const pisac = Number($('#pisacPretraga').val());

            const filtrirano = knjige.filter(element => {
                return element.naslov.toLowerCase().includes(pretraga) && (pisac == 0 || Number(element.pisac_id) == pisac) && (zanr == 0 || Number(element.zanr_id) == zanr);
            })
            $('#tabela').html('');
            for (let knjiga of filtrirano) {
                $('#tabela').append(`
                    <tr>
                        <td>${knjiga.id}</td>
                        <td>${knjiga.naslov}</td>
                        <td>${knjiga.isbn}</td>
                        <td>${knjiga.broj_strana}</td>
                        <td>${knjiga.pisac_id ? (knjiga.pisac_ime + ' ' + knjiga.pisac_prezime) : 'Nema'}</td>
                        <td>${knjiga.zanr}</td>
                        <td> 
                            <button class='btn btn-danger' onClick="otvoriIzmenu(${knjiga.id})">Izmeni</button>    
                            <button class='btn btn-danger' onClick="obrisi(${knjiga.id})">Obrisi</button>    
                        </td>
                    </tr>
                `)
            }
        }
        function obrisi(id) {
            $.post("./server/knjiga.php", {
                metoda: 'delete',
                id
            }).then(val => {
                val = JSON.parse(val);
                if (!val.status) {
                    alert(val.error);
                    return;
                }
                ucitajKnjige();
            });
        }

        function popuniComboBox(id, data, valF, textF) {
            for (let element of data) {
                $('#' + id).append(`
                <option value='${valF(element)}'>${textF(element)}</option> 
                `)
            }
        }
        function otvoriIzmenu(id) {
            selId = id;
            const knjiga = knjige.find(e => e.id == id);
            $('#formaKnjiga').val(knjiga ? 'Izmeni knjigu' : 'Kreiraj knjigu');
            $('#vratiSe').attr('hidden', knjiga === undefined);
            $('#naslov').val(knjiga?.naslov || '');
            $('#isbn').val(knjiga?.isbn || '');
            $('#opis').val(knjiga?.opis || '');
            $('#brojStrana').val(knjiga?.broj_strana || '');
            $('#zanr').val(knjiga?.zanr_id || '');
            $('#pisac').val(knjiga?.pisac_id || '');
        }
    </script>
</body>

</html>