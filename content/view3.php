<?php
	function printParagraph() {
		echo 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus suscipit mollis felis, eu tincidunt lacus pellentesque et. Nunc in arcu fermentum, efficitur orci non, scelerisque turpis. Vestibulum leo nulla, egestas at vehicula vitae, condimentum eget magna. Sed condimentum sit amet tortor at ultrices. Nullam condimentum, nibh id elementum malesuada, orci diam suscipit ante, vel facilisis felis nisi in nulla. Donec at vestibulum enim. Maecenas odio felis, lacinia vel leo non, euismod ultricies velit. Curabitur fermentum, diam eget ultrices eleifend, odio magna eleifend lectus, nec lacinia ipsum est id tellus. Praesent iaculis tortor nulla, id suscipit elit tincidunt vitae. Ut varius iaculis convallis. Maecenas quis vestibulum justo, eu dictum mi. ';
	}
?>

<div class="container">
	<h4>ROSTER</h4>
	The Roster narratives for C.S. 3.2.8 are organized as follows:

	<ol type="I" style="line-height:1.65em;">
		<li><a href="#I">President/Chief Executive Officeer (CEO)</a></li>
		<li><a href="#II">Vice Presidents</a></li>
		<li><a href="#III">President's Direct Reports</a></li>
		<li><a href="#IV">Deans Council</a>
			<ol type="A">
				<li><a href="#IVA">Academic Deans</a></li>
				<li><a href="#IVB">Other Academic Officers</a></li>
			</ol>
		</li>
		<li><a href="#V">Associate and Assistant Vice Presidents</a>
			<ol type="A">
				<li><a href="#VA">Associate Vice Presidents</a></li>
				<li><a href="#VB">Assistant Vice Presidents</a></li>
			</ol>
		</li>
		<li><a href="#VI">Distance Edication and Off-Campus Instructional Sites</a></li>
		<li style="list-style:none; text-indent:-1.3em;"><a href="#appendixA">Appendix A</a></li>
	</ol>

	<hr />

	<a name="I"></a>
	<h5>I. President/Chief Executive Officer</h5>
	<p>
		<u>President/Chief Executive Officer (CEO):</u> Elmira Mangum (Ph.D. in Edication Leadership and Policy)[5]<a name="6" class="ref">[6]</a> has served as the 
		<?= printParagraph() ?>
	</p>

	<a name="II"></a>
	<h5>II. Vice Presidents</h5>
	<p>
		<?= printParagraph() ?>
	</p>
	<ol>
		<li>
			<u>Provost and Vice President of Academic Affairs:</u>
			<?= printParagraph() ?>
			<br><br>
		</li>
		<li>
			<u>The Interim Vice President for Finance and Administration (CFO):</u>
			Angela Poole (MBA, CPA) has <?= printParagraph() ?>
			<br><br>
		</li>
		<li>
			<u>Vice President for Student Affairs:</u>
			<?= printParagraph() ?>
			<br><br>
		</li>
		<li>
			<u>Vice President for Research</u>
			<?= printParagraph() ?>
			<br><br>
		</li>
		<li>
			<u>Vice President for Audit and Compliance</u>
			<?= printParagraph() ?>
			<br><br>
		</li>
		<li>
			<u>Vice President for University Advancement</u>
			<?= printParagraph() ?>
			<br><br>
		</li>
	</ol>

	<a name="III"></a>
	<h5>III. President's Direct Reports</h5>
	<p>
		<?= printParagraph() ?>
	</p>
	<p>
		<?= printParagraph() ?>
	</p>
	<p>
		<?= printParagraph() ?>
	</p>

	<a name="IV"></a>
	<h5>IV. Deans Council</h5>

	<a name="IVA"></a>
	<h5 style="text-indent: 3em;">A. Academic Deans</h5>
	<p>
		<?= printParagraph() ?>
	</p>

	<a name="IVB"></a>
	<h5 style="text-indent: 3em;">B. Other Academic Officers</h5>
	<p>
		<?= printParagraph() ?>
	</p>

	<a name="V"></a>
	<h5>V. Associate and Assistant Vice Presidents</h5>
	<p>
		<?= printParagraph() ?>
	</p>

	<a name="VA"></a>
	<h5 style="text-indent: 3em;">A. Associate Vice Presidents</h5>
	<p>
		<?= printParagraph() ?>
	</p>

	<a name="VB"></a>
	<h5 style="text-indent: 3em;">B. Assistant Vice Presidents</h5>
	<p>
		<?= printParagraph() ?>
	</p>

	<a name="VI"></a>
	<h5>VI. Distance Edication and Off-Campus Instructional Sites</h5>
	<p>
		<?= printParagraph() ?>
	</p>

	<hr />

	<a name="appendixA"></a>

	<h4>DOCUMENTATION</h4>
	<h5>3.2.8 Appendix A</h5>
	<ol>
		<?php
			for ($i = 0; $i < 67; $i++) {
				if ($i == 5)
					echo '<li><a href="#6">Elmira Mangum, President and CEO</a></li>';
				else if ($i == 0)
					echo '<li><a href="?page=view2#1">FAMU Board of Trustees (BOT) Regulation 10.015</a></li>';
				else
					echo '<li>&nbsp;</li>';
			}
		?>
	</ol>
</div>
