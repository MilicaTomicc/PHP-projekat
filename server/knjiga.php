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
        echo json_encode($broker->izmeni('delete from from  pisac where id='.$id));
        exit;
    }
    if($metoda=='create'){
        $ime=$_POST['ime'];
        $prezime=$_POST['prezime'];
        echo json_encode($broker->izmeni("insert into pisac (ime,prezime) values ('".$ime."','".$prezime."')"));
    }

    echo json_encode([
        'status'=>false,
        'error'=>'Metoda nije prosledjena'
    ]);
?>