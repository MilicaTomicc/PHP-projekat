<?php
    include './broker.php';
   
    $metoda='';

    if(isset($_GET['metoda'])){
        $metoda=$_GET['metoda'];
    }
    if(isset($_POST['metoda'])){
        $metoda=$_POST['metoda'];
    }
    $broker=Broker::getBroker();
    if($metoda=='read'){
        echo json_encode($broker->vratiKolekciju("select k.*, z.naziv as 'zanr', p.ime as 'pisac_ime', p.prezime as 'pisac_prezime' from knjiga k inner join zanr z on(k.zanr_id=z.id) left join pisac p on (k.pisac_id=p.id)"));
        exit;
    }
    if($metoda=='delete'){
        $id=$_POST['id'];
        if(!isset($id) || !intval($id)){
            echo json_encode([
                'status'=>false,
                'error'=>'ID nije odgovarajuci'
            ]);
            exit;
        }
        echo json_encode($broker->izmeni('delete from  knjiga where id='.$id));
        exit;
    }
    if($metoda=='create'){
        $naslov=$_POST['naslov'];
        $opis=$_POST['opis'];
        $brojStrana=$_POST['brojStrana'];
        $isbn=$_POST['isbn'];
        $zanr=$_POST['zanr'];
        $pisac=$_POST['pisac'];
        echo json_encode($broker->izmeni("insert into knjiga (naslov,opis,broj_strana,isbn,pisac_id,zanr_id) values ('".$naslov."','".$opis."',".$brojStrana.",'".$isbn."',".$pisac.",".$zanr.")"));
        exit;
    }
    if($metoda=='update'){
        $id=$_POST['id'];
        if(!isset($id) || !intval($id)){
            echo json_encode([
                'status'=>false,
                'error'=>'ID nije odgovarajuci'
            ]);
            exit;
        }
        $naslov=$_POST['naslov'];
        $opis=$_POST['opis'];
        $brojStrana=$_POST['brojStrana'];
        $isbn=$_POST['isbn'];
        $zanr=$_POST['zanr'];
        $pisac=$_POST['pisac'];
       echo json_encode($broker->izmeni("update knjiga set naslov='".$naslov."',opis='".$opis."',isbn='".$isbn."',broj_strana=".$brojStrana.",pisac_id=".$pisac.",zanr_id=".$zanr." where id=".$id));
        exit;
    }
    echo json_encode([
        'status'=>false,
        'error'=>'Metoda nije prosledjena'
    ]);
?>