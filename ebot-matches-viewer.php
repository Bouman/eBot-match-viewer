<!-- Ici Le CSS du tableau d'affichage -->
<style>
table.ebot tr:nth-child(odd){
  background-color:#282828 transparent;
}
table.ebot {
	border: 0pt;
}
th.ebot {
	border: 1pt solid grey;
	-moz-border-radius:10px;
	-webkit-border-radius:10px;
	border-radius:10px;
}
tr.border_bottom td {
	border-bottom: 1pt solid grey;
	border-right: 0pt;
}
</style>
<!-- FIN du CSS -->
<?php
/**
* @package eBot-Matches-Viewer
* @version 2.0.0
Plugin Name: eBot Matches Viewer
Plugin URI: https://github.com/Asso-nOStReSs/eBot-matches-viewer
Description: Un simple widget pour intégrer les matchs de l'eBot sur votre site communautaire.
Author: Boudjelal Yannick *Bouman*
Version: 2.0.0
Author URI: https://www.asso-respawn.fr
*/

add_action ('widgets_init', 'emv_register_widget');

function emv_register_widget () {
    return register_widget('emv_widget');
}

class emv_widget extends WP_Widget{

	public function emv_widget() {
		$options = array(
			"classname"=>"ebot-matches",
			"description"=>"Affiche les scrores, effectuer avec l'eBot sur vos serveurs."
		);
	/*	$control = array(
			"width"=>1000,
			"height"=>500
		);
	*/
		$this->WP_Widget("emv-ebot-matches","eBoT Matches Viewer",$options);
	}
	
	/**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
    */
	public function widget($args,$instance){
		extract($args);
		echo $before_widget;
		echo $before_title.$instance["titre"].$after_title;
		?>
		<?	
				/* Connection Distante mysql */
				$host= $instance["host"];
				$port= $instance["port"];
				$dbnamedist= $instance["dbnamedistant"];
				$userdist= $instance["userdistant"];
				$passworddist= $instance["passworddistant"];
				$nbrmax= $instance["nbrmax"];
					try{
					$bdd = new PDO('mysql:host='.$host.';port='.$port.';dbname='.$dbnamedist.'', ''.$userdist.'', ''.$passworddist.'');
					$affreq = $bdd->prepare('SELECT * FROM matchs ORDER BY id DESC LIMIT 0, '.$nbrmax.'');
					$affreq->execute();
					}
					catch (Exception $e)
					{
							die('Erreur : ' . $e->getMessage());
					}			
		
		
			echo'<table class="ebot">';
			echo "<tr><th class='ebot'>#Id</th><th class='ebot'>Score</th></tr>";
					while($row = $affreq->fetch(PDO::FETCH_ASSOC)){
						$team1name= $row['team_a_name'];
						$team2name= $row['team_b_name'];
						$team1scr= $row['score_a'];
						$team2src= $row['score_b'];

						echo "<tr class='border_bottom'><td>";
						echo $row['id'];
						echo "</td><td>";
							if($team1scr>$team2src) 
								echo '<strong>'.$team1name.'&nbsp;-&nbsp;<font color="green">'.$team1scr.'</strong></font>&nbsp;:&nbsp;<font color="red">'.$team2src.'</font>&nbsp;-&nbsp;'.$team2name.'';
							elseif($team1scr<$team2src) 
								echo ''.$team1name.'&nbsp;-&nbsp;<font color="red">'.$team1scr.'</font>&nbsp;:&nbsp;<font color="green">'.$team2src.'</font>&nbsp;-&nbsp;<strong>'.$team2name.'</strong>';
							else 
								echo ''.$team1name.'&nbsp;-&nbsp;<font color="bleue">'.$team1scr.'</font>&nbsp;:&nbsp;<font color="bleue">'.$team2src.'</font>&nbsp;-&nbsp;'.$team2name.'';
						echo "</td></tr>";
					}
			echo'</table>';

		echo $after_widget;
		$bdd == null;
	}
	
	/**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
    */
	public function form( $instance ) {
		$instancedefaut = array(
			"titre" => "eBoT Matches",
			"nbrmax" => "5",
			"port" => "3306",
			"userdistant" => "ebotv3",
			"dbnamedistant" => "ebotv3"
		);
		$instance = wp_parse_args($instance,$instancedefaut)
		?>
			<div id="form">
			<p>
				<label for="<?php echo $this->get_field_id("titre"); ?>">Titre : </label>
				<input value="<?echo $instance["titre"];?>" name="<?php echo $this->get_field_name("titre"); ?>" id="<?php echo $this->get_field_id("titre"); ?>" type="text"/>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id("nbrmax"); ?>">Nombre max de match : </label>
				<input value="<?echo $instance["nbrmax"];?>" name="<?php echo $this->get_field_name("nbrmax"); ?>" id="<?php echo $this->get_field_id("nbrmax"); ?>" type="text" maxlength="1"/>
			</p>
			</div>
			<hr>
			<div id="A" class="divoption">
				<p>Connexion Database eBot :</p>
					<p>
						<label for="<?php echo $this->get_field_id("host"); ?>">Ip du Host : </label>
						<input value="<?echo $instance["host"];?>" name="<?php echo $this->get_field_name("host"); ?>" id="<?php echo $this->get_field_id("host"); ?>" type="text"/>
					</p>
					<p>
						<label for="<?php echo $this->get_field_id("port"); ?>">Port : </label>
						<input value="<?echo $instance["port"];?>" name="<?php echo $this->get_field_name("port"); ?>" id="<?php echo $this->get_field_id("port"); ?>" type="text"/>
					</p>
					<p>
						<label for="<?php echo $this->get_field_id("dbnamedistant"); ?>">Nom de la Base de donnée : </label>
						<input value="<?echo $instance["dbnamedistant"];?>" name="<?php echo $this->get_field_name("dbnamedistant"); ?>" id="<?php echo $this->get_field_id("dbnamedistant"); ?>" type="text"/>
					</p>
					<p>
						<label for="<?php echo $this->get_field_id("userdistant"); ?>">Utilisateur "login" : </label>
						<input value="<?echo $instance["userdistant"];?>" name="<?php echo $this->get_field_name("userdistant"); ?>" id="<?php echo $this->get_field_id("userdistant"); ?>" type="text"/>
					</p>
					<p>
						<label for="<?php echo $this->get_field_id("passworddistant"); ?>">Password : </label>
						<input value="<?echo $instance["passworddistant"];?>" name="<?php echo $this->get_field_name("passworddistant"); ?>" id="<?php echo $this->get_field_id("passworddistant"); ?>" type="text"/>
					</p>
					<? if ( !empty($instance['host']) && !empty($instance['port']) && !empty($instance['dbnamedistant']) && !empty($instance['userdistant']) && !empty($instance['passworddistant']) ){
						<p>
							$host= $instance["host"];
							$port= $instance["port"];
							$dbnamedist= $instance["dbnamedistant"];
							$userdist= $instance["userdistant"];
							$passworddist= $instance["passworddistant"];
							try {
								$bdd = new PDO('mysql:host='.$host.';port='.$port.';dbname='.$dbnamedist.'', ''.$userdist.'', ''.$passworddist.'');
								$bdd>setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
								echo "Connexion eBot réussi !";
							}
							catch (Exception $e)
							{
								die('Erreur : ' . $e->getMessage());
							}
						</p>
					} ?>
			</div>
			<hr>
			<p>Merci DeStrO pour l'eBot. Widget dev. par Bouman.</p>
		<?
	}
	
	/**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
    */
	function update($new_instance, $old_instance){
		$instance = array();
        	$instance['titre'] = ( !empty( $new_instance['titre'] ) ) ? strip_tags( $new_instance['titre'] ) : '';
        	$instance['nbrmax'] = ( !empty( $new_instance['nbrmax'] ) ) ? $new_instance['nbrmax'] : '';
		$instance['host'] = ( !empty( $new_instance['host'] ) ) ? $new_instance['host'] : '';
		$instance['port'] = ( !empty( $new_instance['port'] ) ) ? $new_instance['port'] : '';
		$instance['passworddistant'] = ( !empty( $new_instance['passworddistant'] ) ) ? $new_instance['passworddistant'] : '';
        	$instance['userdistant'] = ( !empty( $new_instance['userdistant'] ) ) ? strip_tags( $new_instance['userdistant'] ) : '';
		$instance['dbnamedistant'] = ( !empty( $new_instance['dbnamedistant'] ) ) ? strip_tags( $new_instance['dbnamedistant'] ) : '';
		return $new_instance;
	}
}
?>
