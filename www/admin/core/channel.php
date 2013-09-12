<?php
if(!defined('YOUTORR')){
	die(1);
}
if(isset($_GET['channel']) && isset($_GET['action'])){
	#Deleting channel
	if(!empty($_GET['channel']) && $_GET['action']=='unfollow'){
		$channel=$_GET['channel'];
		$channelID=getChannelId($channel,$db);
		if(!empty($channelID)){
			$conn=$db->prepare("DELETE FROM `user_meta` WHERE `id_user`=:id_user AND `key`='channel' AND `value`=:id_channel");
			$conn->bindParam(':id_channel',$channelID);
			$conn->bindParam(':id_user',$_SESSION['id']);
			#$conn=$db->prepare("UPDATE `channels` SET `delete` = 1 WHERE id = :id");
			#$conn->bindParam(':id',$channelID);
			$conn->execute();
		}
	}
	if($_SESSION['role'] == 100 &&  !empty($_GET['channel']) && $_GET['action']=='delete'){
	$channel=$_GET['channel'];
	$channelID=getChannelId($channel,$db);
	if(!empty($channelID)){
		$conn=$db->prepare("UPDATE `channels` SET `delete` = 1 WHERE id = :id");
		$conn->bindParam(':id',$channelID);
		$conn->execute();
	}

	}
	if($_SESSION['role'] == 100 &&  !empty($_GET['channel']) && $_GET['action']=='undodelete'){
		$channel=$_GET['channel'];
		$channelID=getChannelId($channel,$db);
		if(!empty($channelID)){
			$conn=$db->prepare("UPDATE `channels` SET `delete` = 0 WHERE id = :id");
			$conn->bindParam(':id',$channelID);
			$conn->execute();
		}
	}
	#force recheck
	if(!empty($_GET['channel']) && $_GET['action']=='force'){
		$channel=$_GET['channel'];
		$channelID=getChannelId($channel,$db);
		if(!empty($channelID)){
			$conn=$db->prepare("UPDATE `channels` SET `force` = 1 WHERE id = :id");
			$conn->bindParam(':id',$channelID);
			$conn->execute();
		}

	}
}

if(isset($_GET['channel']) && !empty($_GET['channel'])){
		$getChannel=htmlentities($_GET['channel']);
		$channelID=getChannelId($getChannel,$db);
		if(!empty($channelID)){
			$conn=$db->prepare("SELECT `delete` FROM `channels` WHERE id = :id");
			$conn->bindParam(':id',$channelID);
			$conn->execute();
			$channel=$conn->fetch(PDO::FETCH_ASSOC);
			if(is_dir($config['torrentDataDir']."/$getChannel/")){
				$channelSize=getDirSize($config['torrentDataDir']."/$getChannel/");
				$channelSize=inSize($channelSize,$config['maxSize']);
				$channelSize=substr($channelSize,0,5);
				$channel['size']=$channelSize;
			}
			$channel['videos']=dbCounter('select count(*) as count FROM `videos` WHERE `id_channel` = "'.$channelID.'"',$db);
			$channel['videos_tmp']=dbCounter('select count(*) as count FROM `videos_tmp` WHERE `id_channel` = "'.$channelID.'"',$db);
		}
}
include('views/channel.php');
