<?php
$sid = $_POST['sid'];
include("../getConfig.php");
include($_SERVER['DOCUMENT_ROOT']."/inc/func.php");
class logger {
    public function __construct($status, $lld) {
        $this->status = $status;
        $this->lld = $lld;
        $this->log_ = false;
        $this->handle = false;
        $this->sendLog = 0;
        $this->log_err = false;
        $this->log_warn = false;
        $this->log_time = true;
        $this->cmds = 0;
        $this->warns = 0;
        $this->errors = 0;
        $this->infos = 0;
        $this->undefineds = 0;
        $this->joined = 0;
        $this->left = 0;
        $this->loglines = 0;
    }
    private function getlog() {
        if (file_exists(SERVER_LOG_DIR)) {
            $this->handle = fopen(SERVER_LOG_DIR, "r+");
            return true;
        } else return false;
    }
	private function customFiltersLines($log=false) {
		$customFilter = explode (", ", REGEX_FILTERS_LINE);
		if(empty($customFilter))return true;
		if(!$log) {
			$log = $this->log_;
		}
		foreach($customFilter as $ft) {
			if(preg_match($ft, $log))return false;
		}
		return true;
	}
	private function customFiltersCensor() {
		$customFilter = explode(", ", REGEX_FILTERS_CENSOR);
		if(empty($customFilter))return;
		foreach($customFilter as $ft) {
			$new = explode(" => ", $ft);
			if(preg_match($new[0], $this->log_)) {
				$this->log_ = preg_replace($new[0], $new[1], $this->log_);
			}
		}
		return;
	}
    private function filters() {
        $this->log_ = preg_replace("/\[0;30;22m/", "<span style='color: #000000;'>", $this->log_);
        $this->log_ = preg_replace("/\[0;34;22m/", "<span style='color: #0000AA;'>", $this->log_);
        $this->log_ = preg_replace("/\[0;32;22m/", "<span style='color: #00AA00;'>", $this->log_);
        $this->log_ = preg_replace("/\[0;36;22m/", "<span style='color: #00AAAA;'>", $this->log_);
        $this->log_ = preg_replace("/\[0;31;22m/", "<span style='color: #AA0000;'>", $this->log_);
        $this->log_ = preg_replace("/\[0;35;22m/", "<span style='color: #AA00AA;'>", $this->log_);
        $this->log_ = preg_replace("/\[0;33;22m/", "<span style='color: #FFAA00;'>", $this->log_);
        $this->log_ = preg_replace("/\[0;37;22m/", "<span style='color: #AAAAAA;'>", $this->log_);
        $this->log_ = preg_replace("/\[0;30;1m/", "<span style='color: #555555;'>", $this->log_);
        $this->log_ = preg_replace("/\[0;34;1m/", "<span style='color: #5555FF;'>", $this->log_);
        $this->log_ = preg_replace("/\[0;32;1m/", "<span style='color: #55FF55;'>", $this->log_);
        $this->log_ = preg_replace("/\[0;36;1m/", "<span style='color: #55FFFF;'>", $this->log_);
        $this->log_ = preg_replace("/\[0;31;1m/", "<span style='color: #FF5555;'>", $this->log_);
        $this->log_ = preg_replace("/\[0;35;1m/", "<span style='color: #FF55FF;'>", $this->log_);
        $this->log_ = preg_replace("/\[0;33;1m/", "<span style='color: #FFFF55;'>", $this->log_);
        $this->log_ = preg_replace("/\[0;37;1m/", "<span style='color: #FFFFFF;'>", $this->log_);
        $this->log_ = preg_replace("/\[5m/", "", $this->log_);
        $this->log_ = preg_replace("/\[4m/", "", $this->log_);
        $this->log_ = preg_replace("/\[9m/", "", $this->log_);
        $this->log_ = preg_replace("/\[21m/", "", $this->log_);
        //$this->log_ = preg_replace("/\\[m/", "</span>", $this->log_);
		$this->log_ = preg_replace("/\\[m/", "</span>", $this->log_);
        $this->type();
    }
    private function time_() {
        if ($this->status == "clu" || $this->status == "gh") {
            if (preg_match("/\\[(\d{2}):(\d{2}):(\d{2})\\]/", $this->log_, $time)) {
                if (!$this->log_time) {
                    $this->log_time = true;
                }
                $this->log_ = preg_replace("/\\[(\d{2}):(\d{2}):(\d{2})\\]/", "", $this->log_);
                $this->log_ = "<div class='row'><span class='time'>$time[1]:$time[2]:$time[3]</span>$this->log_";
            } else {
                $this->log_time = false;
                $this->log_ = "<div class='row'><span class='time'></span>$this->log_";
            }
        }
    }
    private function type() {
        if (preg_match("/issued server command:/", $this->log_)) {
            if ($this->status == "clu" || $this->status == "gh") {
                $this->log_ = "<div class='log cmd'>" . $this->log_ . "</div>";
            } elseif ($this->status == "ilu") {
                $this->cmds++;
            }
        } elseif (preg_match("/logged in with entity id/", $this->log_)) {
            if ($this->status == "clu" || $this->status == "gh") {
                $this->log_ = "<div class='log joined'> <i class='fa fa-plus'></i> " . $this->log_ . "</div>";
            } elseif ($this->status == "ilu") {
                $this->joined++;
            }
        } elseif (preg_match("/UUID of player/", $this->log_)) {
            if ($this->status == "clu" || $this->status == "gh") {
                $this->log_ = "<div class='log joined' style='padding-left: 32px'> " . $this->log_ . "</div>";
            } elseif ($this->status == "ilu") {
                $this->joined++;
            }
        } elseif (preg_match("/[a-zA-Z0-9_]{1,16} lost connection:/", $this->log_)) {
            if ($this->status == "clu" || $this->status == "gh") {
                $this->log_ = "<div class='log left' style='padding-left: 32px'> " . $this->log_ . "</div>";
            } elseif ($this->status == "ilu") {
                $this->left++;
            }
        } elseif (preg_match("/[a-zA-Z0-9_]{1,16} left the game/", $this->log_)) {
            if ($this->status == "clu" || $this->status == "gh") {
                $this->log_ = "<div class='log left'> <i class='fa fa-minus'></i> " . $this->log_ . "</div>";
            } elseif ($this->status == "ilu") {
                $this->left++;
            }
        } elseif (preg_match("/\[Async Chat Thread - #([0-9]+)\/INFO\]/", $this->log_)) {
            if ($this->log_err) {
                $this->log_err = false;
            } elseif ($this->log_warn) {
                $this->log_warn = false;
            }
            if ($this->status == "clu" || $this->status == "gh") {
                $this->log_ = "<div class='log info'>" . $this->log_ . "</div>";
            } elseif ($this->status == "ilu") {
                $this->infos++;
            }
        } elseif (preg_match("/\\[Server thread\\/INFO\\]:/", $this->log_)) {
            if ($this->log_err) {
                $this->log_err = false;
            } elseif ($this->log_warn) {
                $this->log_warn = false;
            }
            if ($this->status == "clu" || $this->status == "gh") {
                $this->log_ = "<div class='log info'>" . $this->log_ . "</div>";
            } elseif ($this->status == "ilu") {
                $this->infos++;
            }
        } elseif (preg_match("/\\[Server thread\\/INFO\\]:/", $this->log_)) {
            if ($this->log_err) {
                $this->log_err = false;
            } elseif ($this->log_warn) {
                $this->log_warn = false;
            }
            if ($this->status == "clu" || $this->status == "gh") {
                $this->log_ = "<div class='log info'>" . $this->log_ . "</div>";
            } elseif ($this->status == "ilu") {
                $this->infos++;
            }
        } elseif (preg_match("/\\[Server thread\\/ERROR\\]:/", $this->log_)) {
            if ($this->status == "clu" || $this->status == "gh") {
                $this->log_ = "<div class='log error'> <i class='fa fa-times-circle'></i> " . $this->log_ . "</div>";
                $this->log_err = true;
            } elseif ($this->status == "ilu") {
                $this->errors++;
            }
        } else {
            if ($this->status == "clu" || $this->status == "gh") {
                if ($this->log_err) {
                    $this->log_ = "<div class='log error log_spacer'>" . $this->log_ . "</div>";
                } elseif ($this->log_warn) {
                    $this->log_ = "<div class='log warning log_spacer'>" . $this->log_ . "</div>";
                } else {
                    $this->log_ = "<div class='log undefined'>" . $this->log_ . "</div>";
                }
            } elseif ($this->status == "ilu") {
                if ($this->log_err && $this->log_warn) {
                    $this->undefineds++;
                }
            }
        }
		$this->log_ = preg_replace("/\&lt;([a-zA-Z0-9_]{1,16})\&gt;/", "<b><span style='color: #0bc'> $1</span>:</b>&nbsp;", $this->log_);
		$this->log_ = preg_replace("/\[Async Chat Thread - #([0-9]+)\/INFO\]:/", "<div style='color: #09c; padding: 0px 0px 0px 0px;'>[User Chat]&nbsp;&nbsp;</div>", $this->log_);
		$this->log_ = preg_replace("/\\[Server thread\\/INFO\\]:/", "<div style='color: #0c0; padding: 0px 10px 0px 0px;'>[Console]</div>", $this->log_);
        $this->log_ = preg_replace("/\\[Server\\]/", "<div style='color: #FF55FF; padding: 0px 5px 0px 0px;'>[Server]</div>", $this->log_);
        $this->log_ = preg_replace("/\\[Server thread\\/ERROR\\]:/", "<div style='color: #09c; padding: 0px 10px 0px 0px;'>[Server]</div>", $this->log_);
        $this->log_ = preg_replace("/\\[Server thread\\/WARN\\]:/", "<div style='color: #09c; padding: 0px 10px 0px 0px;'>[Server]</div>", $this->log_);
        $this->log_ = preg_replace("/\\[SYSTM\\]:/", "<div style='color: #fc3; padding: 0px 10px 0px 0px;'>[SYSTM]</div>", $this->log_);
		$this->customFiltersCensor();
        if ($this->status == "ilu") {
            $this->loglines++;
        }
        $this->log_ = $this->log_ . "</div>";
        $this->time_();
    }
    public function run() {
		$lines = 0;
		$tlines = 1; // temporary line counting (number of \n's)
		$limit = 500;
		$pos = -2;
		define("PHP_EOL", "\n");
        if ($this->status !== "lld") {
            if($this->getlog()) {
                while(fseek($this->handle, $pos, SEEK_END) !== -1) { // go from the bottom of the file up 500 lines
                    $char = fgetc($this->handle);
                    if($char == PHP_EOL) {
                        if($this->customFiltersLines(fgets($this->handle))) {
							$tlines++;
						}
                        if($tlines > $limit) {
                            break;
                        }
                    }
                    $pos--;
                }
                if($tlines <= $limit)rewind($this->handle);
                while(!feof($this->handle)) {
                    //$this->log_ = stream_get_line($this->handle, 10000000, "\n"); // get line, max buffer = big #
					$this->log_ = fgets($this->handle);
					$this->log_ = str_replace("\n", "", $this->log_);
					$this->log_ = str_replace("\r", "", $this->log_);
					$this->log_ = htmlspecialchars($this->log_);
					if($this->lld == "" || $this->status == "ilu") {
						  $this->sendLog = 1;
					}
                    if ($this->log_ !== "") {
                        if ($this->sendLog == 1) {
                            $this->filters();
                            if ($this->status !== "ilu") {
								$lines++;
                                echo $this->log_;
                            }
                        }
                        if ($this->status == "clu") {
                            if ($this->sendLog == 0) {
                                if ($this->log_ == $this->lld) { // it's the line after!
                                    $this->sendLog = 1;
                                } 
                            }
                        }
                    }
                }
                if ($this->status == "ilu") {
                    $u = $this->joined - $this->left;
                      return "<div class='block block-info'><i class='fa fa-info-circle'></i>" . $this->infos . "</div>
							  <div class='block block-warning'><i class='fa fa-warning'></i>" . $this->warns . "</div>
							  <div class='block block-errors'><i class='fa fa-times-circle'></i>" . $this->errors . "</div>
							  <div class='block block-cmds'><i class='fa fa-terminal'></i>" . $this->cmds . "</div>
							  <div class='block block-undefined'><i class='fa fa-question-circle'></i>" . $this->undefineds . "</div>
							  <div class='block block-undefined'><i class='fa fa-file-text'></i>" . $this->loglines . "</div>
							  <div class='block block-undefined'><i class='fa fa-user'></i>" . $u . "</div>";
                }
            } else return "Error: server log file: '" . SERVER_LOG_DIR . "' doesn't exist.";
        } else { // lld
            $data = file(SERVER_LOG_DIR);
            $this->log_ = $data[count($data) - 1]; // eof - 1
            $this->log_ = str_replace("\n", "", $this->log_);
            $this->log_ = str_replace("\r", "", $this->log_);
			$this->log_ = htmlspecialchars($this->log_);
            return $this->log_;
        }
    }
}
?>