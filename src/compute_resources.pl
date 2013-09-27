#!/usr/bin/perl

use Digest::MD5 qw(md5 md5_hex md5_base64);

@rules = (
	["redirect(ed)?\\.tpl", 1],
	["\\.tpl", 0],
	
	["view/license.php", 0],
	["view/themes.php", 2],
	["view/translate.php", 2],
	["view/translatelist.php", 2],
	["view/settings.php", 2],
	["view/updates.php", 2],
	["view/features.php", 2],
	["view/performance.php", 2],
	["view/avatar.php", 2],
	["view/permissions.php", 2],
	["view/agent.php", 2],
	["view/agents.php", 2],
	["view/group.php", 2],
	["view/groupmembers.php", 2],
	["view/groups.php", 2],
	["view/operator_groups.php", 2],
	["view/gen_button.php", 2],
	["view/install_err.php", 2],
	["view/install_index.php", 2],
	["view/.*\\.php", 1],
	
	["install/.*\\.php", 2],
	
	["operator/themes.php", 2],
	["operator/translate.php", 2],
	["operator/settings.php", 2],
	["operator/updates.php", 2],
	["operator/features.php", 2],
	["operator/performance.php", 2],
	["operator/avatar.php", 2],
	["operator/permissions.php", 2],
	["operator/operator.php", 2],
	["operator/operators.php", 2],
	["operator/group.php", 2],
	["operator/groupmembers.php", 2],
	["operator/groups.php", 2],
	["operator/opgroups.php", 2],
	["operator/getcode.php", 2],
	["operator/.*\\.php", 1],
	
	["mibew/client.php", 0],
	["mibew/leavemessage.php", 0],
	["mibew/captcha.php", 0],
	["mibew/license.php", 0],
	["mibew/mail.php", 0],	

	["libs/operator_settings.php", 2],
	["mibew/libs/chat.php", 0],
	["libs/pagination.php", 1],
	["libs/settings.php", 2],
	["libs/groups.php", 2],
	["libs/demothread.php", 2],

	["mibew/thread.php", 0],
	["mibew/b.php", 0],
	["mibew/button.php", 0],
	["mibew/index.php", 0],

	["mibew/libs/.*\\.php", 1],
);

%messagekeys = (
	"localeid" => 0,
	"output_charset" => 0,
	"output_encoding" => 0,
	"harderrors.header" => 0,

	"errors.required" => 0,
	"errors.wrong_field" => 0,	
	"errors.file.move.error" => 2,
	"errors.invalid.file.type" => 2,
	"errors.file.size.exceeded" => 2,

	"permission.admin" => 1,
	"permission.takeover" => 1,
	"permission.viewthreads" => 1,
	
	"chat.thread.state_chatting_with_agent" => 1,
	"chat.thread.state_closed" => 1,
	"chat.thread.state_loading" => 1,
	"chat.thread.state_wait" => 1,
	"chat.thread.state_wait_for_another_agent" => 1,

	"clients.queue.chat" => 1,
	"clients.queue.prio" => 1,
	"clients.queue.wait" => 1,
);


$mibewPath = "mibew";

%urls = ();

%usermessages = ();
%operatormessages = ();
$current_level = 0;

sub usemsg($) {
	my ($m) = @_;
	$messagekeys{$m} = exists $messagekeys{$m} && $messagekeys{$m} < $current_level ? $messagekeys{$m} : $current_level;
	if($current_level == -1) {
		print " .. $m\n";
	}
}

sub file_content($) {
	my $input = $_[0];
	open( IN1, "< $input" ) or die "cannot find file $input";
	my $oldslash = $/;
	$/ = EOI;
	$content = <IN1>;
    close( IN1 );
	if($content =~ s/\r//g) {
	    open( OUT1, "> $input") or die "cannot fix $input";
	    print OUT1 $content;
	    close(OUT1);
	}
	$/ = $oldslash;
	return $content;
}

sub process_tpl($) {
	my ($filename) = @_;
	my $m = file_content($filename);
	while( $m =~ /\${msg:([\w\.]+)(,[\w\.]+)?}/g ) {
		usemsg($1);
	}
	while( $m =~ /\${url:([\w\.]+)(,[\w\.]+)?}/g ) {
		usemsg($1);
		$urls{"%$1"} = 1;
	}
}

sub process_php($) {
	my ($source) = @_;
	my $content = file_content($source);
	$content =~ s/<\?xml version=\\"1\.0\\" encoding=\\"UTF-8\\"\?>//;

	while( $content =~ s/<\?(?!xml)(.*?)\?>//s ) {
		my $inner = $1;
		while($inner =~ s/(getlocal|getstring|no_field)2?_?\((.*?)[,\)]//s) {
			my $firstarg = $2;
			if( $firstarg =~ /^["']([\w\.]+)['"]$/) {
				usemsg($1);
			} elsif($firstarg =~ /^\$\w+$/ || $firstarg eq '"$var.header"' || $firstarg eq '"permission.$permid"' || $firstarg eq '$threadstate_key[$thread[\'istate\']]') {
				# skip
			} else {
				print "> unknown: $firstarg\n";
			} 
		}
	}
}

sub file_checksum($) {
    my ($source) = @_;
    if($source =~ /\.(png|gif|jpg|ico|wav)$/ || $source =~ /config\.php$/) {
    	return "-";
    }

    my $content = file_content($source);
    return md5_hex($content);
}

@allsources = ();

sub process_one($) {
	my($source) = @_;
	push @allsources, $source unless $source =~ /$mibewPath\/locales/ && $source !~ /$mibewPath\/locales\/(en|names)/ || $source =~ /\/package$/;

	if($source !~ /\.(php|tpl)$/) {
		return;
	}
	
	$current_level = -1;

	A: foreach $rule (@rules) {
		my $key = $$rule[0];
		if($source =~ /$key/) {
			$current_level = $$rule[1];
			last A;
		}
	}
	if($current_level < 0 || $current_level > 2) {
		print "not detected for: $source\n";
		$current_level = 0;
	}

	if($source =~ /\.php$/) {
		# print "$source ($current_level)\n";
		process_php($source);
	} elsif($source =~ /\.tpl$/) {
		process_tpl($source);
	} else {
		#print ". $source\n";
	}
}

sub process_files($) {
	my($from) = @_;
	opendir(DIR, $from) || die "can't opendir $from: $!";
	my @content = readdir(DIR);
	closedir DIR;

	for(grep { -f "$from/$_" && ($_ !~ /^\./ || $_ eq ".htaccess") } @content) {
		process_one("$from/$_");
	}
	for(grep { -d "$from/$_" && $_ !~ /^\./ } @content) {
		process_files("$from/$_");
	}
}

process_files($mibewPath);

# fix
$messagekeys{'page.analysis.userhistory.title'} = 1;
$messagekeys{'errors.failed.uploading.file'} = 2;


open( OUT, "> $mibewPath/locales/names/level1") or die "cannot write file, $!";
for $key(sort grep { $messagekeys{$_} == 0 } keys %messagekeys) {
	print OUT "$key\n";
}
close( OUT );

open( OUT, "> $mibewPath/locales/names/level2") or die "cannot write file, $!";
for $key(sort grep { $messagekeys{$_} == 1 } keys %messagekeys) {
	print OUT "$key\n";
}
close( OUT );

open( OUT, "> $mibewPath/install/package") or die "cannot write file, $!";
for $key(sort @allsources) {
    $digest = file_checksum($key);
    $key =~ s/$mibewPath\///;
    print OUT "$key $digest\n";
}
close( OUT );
