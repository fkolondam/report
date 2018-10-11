<?php
function getStartAndEndDate($week, $year=2018)
{

    $time = strtotime("1 January $year", time());
    $day = date('w', $time);
    $time += ((7*$week)+1-$day)*24*3600;
    $return[0] = date('Y-n-j', $time);
    $time += 6*24*3600;
    $return[1] = date('Y-n-j', $time);
    return $return;
}
?>
<html>
	<head>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	 	<script>
	  		$( function() {
	    		$( "#datepicker" ).datepicker({ dateFormat: 'yy/mm/dd' });
	    		$( "#datepicker2" ).datepicker({ dateFormat: 'yy/mm/dd' });
	  		} );
	  	</script>

	</head>
	<body>
	<div id="wrapper">

	<?php include "koneksi.php"; ?>
	<h1>Laporan Data Penjualan</h1>
	<form action="" method="post">
		<fieldset>
		<legend>Pilih Opsi Laporan :</legend>
		<select name="OPSI_LAPORAN">
			<optgroup label="Penjualan per Salesman">
				<option value="SLS_BY_SLSMEN_SHOW_BY_WEEK">Tampil per Pekan</option>
				<option value="SLS_BY_SLSMEN_SHOW_BY_CUST">Tampil per Customer</option>
				<option value="SLS_BY_SLSMEN_SHOW_BY_PRINSIPAL">Tampil per Prinsipal</option>
			</optgroup>
			<optgroup label="Penjualan per Customer">
				<option value="SLS_BY_CUST_SHOW_BY_WEEK">Tampil per Pekan</option>
				<option value="SLS_BY_CUST_SHOW_BY_PRINSIPAL">Tampil per Prinsipal</option>
			</optgroup>
			<optgroup label="Penjualan per Pekan">
				<option value="SLS_BY_WEEK_SHOW_BY_SLSMEN">Tampil per Salesmen</option>
				<option value="SLS_BY_WEEK_SHOW_BY_CUST">Tampil per Customer</option>
				<option value="SLS_BY_WEEK_SHOW_BY_PRINSIPAL">Tampil per Prinsipal</option>
			</optgroup>
		</select>
		</fieldset>
		<fieldset>
		<legend>Range waktu Data :</legend>
		<p>Dari: <input type="text" id="datepicker" name="tgl1" autocomplete="off">
		Sampai: <input type="text" id="datepicker2" name="tgl2" autocomplete="off"></p>	
		</fieldset>
		<hr />
		 <input type="submit" value="cari" name="cari" style="width: 100%;padding: 5px;" />
	</form>
	<?php
		$z="0";
		if (isset($_POST['cari'])) {
			
			$tgl1=$_POST['tgl1'];
			$tgl2=$_POST['tgl2'];
			$opsi_lap = $_POST['OPSI_LAPORAN'];
			echo "<h3>Menampilkan data penjualan Tanggal : $tgl1 s.d $tgl2</h3>";
			if($opsi_lap == "SLS_BY_SLSMEN_SHOW_BY_WEEK"){
			/*$sql = "SELECT 
			dbo.trs_sls_hdr.Sls_Number,
			dbo.trs_sls_hdr.Sls_Date,
			DISTINCT(dbo.trs_sls_hdr.Cus_Code),
			dbo.trs_sls_hdr.Sls_slmcd,
			dbo.trs_sls_hdr.Sls_Tvallocal, 
			dbo.Mst_Cust.Cus_Fpname, 
			dbo.Mst_Slsman.Slm_Name 
			FROM dbo.trs_sls_hdr 
			INNER JOIN dbo.Mst_Slsman ON dbo.trs_sls_hdr.Sls_slmcd = dbo.Mst_Slsman.Slm_Code 
			INNER JOIN dbo.Mst_Cust On dbo.trs_sls_hdr.Cus_Code = dbo.Mst_Cust.Cus_Code
			WHERE dbo.trs_sls_hdr.Sls_Date BETWEEN '$tgl1' AND '$tgl2'
			ORDER BY dbo.trs_sls_hdr.Sls_Date ASC";*/
			// 
			$sql = "SELECT dbo.Mst_Slsman.Slm_Code, dbo.Mst_Slsman.Slm_Name FROM dbo.Mst_Slsman";
			//echo "$sql";
			$exeSql = sqlsrv_query($conn,$sql);

			 

			$startDateUnix = strtotime($tgl1);
		    $endDateUnix = strtotime($tgl2);

		    $currentDateUnix = $startDateUnix;

		    $weekNumbers = array();
		    while ($currentDateUnix < $endDateUnix) {
		        $weekNumbers[] = date('W', $currentDateUnix);
		        $currentDateUnix = strtotime('+1 week', $currentDateUnix);
		    }
		    $jlhWeek = count($weekNumbers);
		    //print_r($weekNumbers);

			?>
			<table border="1" cellpadding="2" cellspacing="0" width="100%">
			<tr>
				<td width="4%">Kode Salesmen</td>
				<td width="15%">Nama Salesmen</td>
				<?php
				for($i=0;$i<$jlhWeek;$i++){
				?>
				<td align="center"><?php echo "Week-".$weekNumbers[$i]; ?></td>
				<?php } ?>
				<td align="center">Total</td>
			</tr>
			<?php 
				$subTotal = 0;
				while($row = sqlsrv_fetch_array($exeSql)){
			?>			
			<tr>
				<td><?php echo $row["Slm_Code"]; ?></td>
				<td><?php echo $row["Slm_Name"]; ?></td>
				<?php
				$totWeek = 0;
				for($i=0;$i<$jlhWeek;$i++){
					$awalAkhirWeek = getStartAndEndDate($weekNumbers[$i]-1);
					//$akhirWeek = getStartAndEndDate($weekNumbers[$i]-1)[1];
					// Mencari nilai penjualan per Salesmen dari Week ini
					$sql2 = "SELECT SUM(dbo.trs_sls_hdr.Sls_Tvallocal) as 'TOT' 
							FROM dbo.trs_sls_hdr 
							WHERE dbo.trs_sls_hdr.Sls_slmcd = '$row[Slm_Code]'
							AND dbo.trs_sls_hdr.Sls_Date BETWEEN '$awalAkhirWeek[0]' AND '$awalAkhirWeek[1]'";
					//echo $sql2;
					$exeSql2 = sqlsrv_query($conn,$sql2);
					$resSql2 = sqlsrv_fetch_array($exeSql2);
					$totWeek += $resSql2["TOT"];
				?>
				<td align="right"><?php echo number_format($resSql2["TOT"]); ?></td>
				<?php } ?>
				<td align="right"><?php echo number_format($totWeek); ?></td>
			</tr>
			<?php
				$subTotal += $totWeek;
				}				
		 ?>
		 <tr style="background-color: #eee;">
			<td colspan="2"><strong>S U B    T O T A L</strong></td>
			<?php
				$totWeek = 0;
				for($i=0;$i<$jlhWeek;$i++){
					$awalAkhirWeek = getStartAndEndDate($weekNumbers[$i]-1);
					//$akhirWeek = getStartAndEndDate($weekNumbers[$i]-1)[1];
					// Mencari nilai penjualan per Salesmen dari Week ini
					$sql3 = "SELECT SUM(dbo.trs_sls_hdr.Sls_Tvallocal) as 'TOT' 
							FROM dbo.trs_sls_hdr 
							WHERE dbo.trs_sls_hdr.Sls_Date BETWEEN '$awalAkhirWeek[0]' AND '$awalAkhirWeek[1]'";
					//echo $sql2;
					$exeSql3 = sqlsrv_query($conn,$sql3);
					$resSql3 = sqlsrv_fetch_array($exeSql3);
					$totWeek += $resSql2["TOT"];
				?>
				<td align="right"><?php echo number_format($resSql3["TOT"]); ?></td>
				<?php } ?>
			<td align="right"><?php echo number_format($subTotal); ?></td>
		</tr>

		</table>	
		<?php 
			
		} // END OF OPSI_LAPORAN
		} // END OF SUBMIT
		?>
		</div>	
	</body>
</html>