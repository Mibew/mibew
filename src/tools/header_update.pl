#!/usr/bin/perl

$sourceFolder = "../mibew";

sub file_content($) {
	my $input = $_[0];
	open( IN1, "< $input" ) or die "cannot find file $input";
	$/ = EOI;
	$content = <IN1>;
	$content =~ s/\r//g;
	close( IN1 );
	return $content;
}

$php_header = file_content("header.txt");
$php_header =~ s/\s+$//;

@allfiles = ();

sub process_folder($) {
	my($from) = @_;
	
	opendir(DIR, $from) || die "can't opendir $from: $!";
	my @content = readdir(DIR);
	closedir DIR;

	for(grep { -f "$from/$_" && ($_ !~ /^\./ || $_ eq ".htaccess") } @content) {
		push @allfiles, "$from/$_";
	}
	for(grep { -d "$from/$_" && $_ !~ /^\./ } @content) {
		process_folder("$from/$_");
	}
}

process_folder($sourceFolder);

P: for $phpfile (grep { /\.php$/ } @allfiles) {
	$content = file_content($phpfile);
	$content =~ s/\s+$//g;
	die "not a php: $phpfile" unless $content =~ /^<\?php\n(\/\*.*?\*\/)?/s;
	die "no comment in $phpfile" unless defined($1);
	$comment = $1;
	if($comment =~ /\[external\]/) {
		next P;
	};
	$newcomment = "$php_header";
	$newcomment =~ s/^/ * /gm;
	$newcomment =~ s/\s+$//gm;
	$newcomment = "/*\n$newcomment\n */";
	
	$content =~ s/^(<\?php\n)\/\*.*?\*\//$1$newcomment/s;
	
    open( OUT, "> $phpfile" ) or die "cannot write file: $phpfile\n";
    print OUT $content;
    close( OUT );
}
