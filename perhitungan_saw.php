<?php
include 'koneksi.php';

$hasil = [];

// ambil semua aparatur
$aparatur=mysqli_query($koneksi,"SELECT * FROM aparatur");

while($a=mysqli_fetch_array($aparatur)){

$id_aparatur=$a['id_aparatur'];
$nama=$a['nama'];

$total=0;

// ambil semua kriteria
$kriteria=mysqli_query($koneksi,"SELECT * FROM kriteria");

while($k=mysqli_fetch_array($kriteria)){

$id_kriteria=$k['id_kriteria'];
$bobot=$k['bobot'];
$atribut=$k['atribut'];

// ambil nilai
$nilai=mysqli_query($koneksi,"
SELECT nilai FROM penilaian
WHERE id_aparatur='$id_aparatur'
AND id_kriteria='$id_kriteria'
");

$n=mysqli_fetch_array($nilai);
$nilai_asli=$n['nilai'] ?? 0;

// max & min
$max=mysqli_fetch_array(mysqli_query($koneksi,"
SELECT MAX(nilai) as max FROM penilaian WHERE id_kriteria='$id_kriteria'
"));

$min=mysqli_fetch_array(mysqli_query($koneksi,"
SELECT MIN(nilai) as min FROM penilaian WHERE id_kriteria='$id_kriteria'
"));

// normalisasi
if($atribut=='benefit'){
    $normalisasi = $nilai_asli / ($max['max'] ?: 1);
}else{
    $normalisasi = ($min['min'] ?: 1) / ($nilai_asli ?: 1);
}

// total nilai
$total += $normalisasi * $bobot;

}

// simpan ke array
$hasil[] = [
    'nama' => $nama,
    'nilai' => $total
];

}

// urutkan dari terbesar
usort($hasil, function($a, $b){
    return $b['nilai'] <=> $a['nilai'];
});
?>

<h2>Ranking Aparatur Terbaik</h2>

<table border="1" cellpadding="10">

<tr>
<th>Ranking</th>
<th>Nama Aparatur</th>
<th>Nilai</th>
</tr>

<?php
$rank=1;

foreach($hasil as $h){
?>

<tr>
<td><?php echo $rank++; ?></td>
<td><?php echo $h['nama']; ?></td>
<td><?php echo round($h['nilai'],3); ?></td>
</tr>

<?php } ?>

</table>