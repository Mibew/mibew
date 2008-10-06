#!/usr/bin/perl

##################################################################
# Arguments
##################################################################

$targetFolder = "deploy";
$suffix = "142";

##################################################################
# Copies tree into target folder, preprocess .phps
##################################################################

sub process_dir($$) {
	my ($from,$to) = @_;
	opendir(DIR, $from) || die "can't opendir $from: $!";
    my @content = readdir(DIR);
    closedir DIR;
    mkdir $to;
    
	for(grep { -f "$from/$_" && ($_ !~ /^\./ || $_ eq ".htaccess") } @content) {
		my ($source,$target) = ("$from/$_","$to/$_");

		open (IN,"$source");
		binmode(IN);
		open (OUT,">$target");
		binmode(OUT);
		print OUT $buffer while (read (IN,$buffer,65536));
	}
	
	for(grep { -d "$from/$_" && $_ !~ /^\./ } @content) {
		process_dir("$from/$_","$to/$_");
	} 
}

##################################################################
# Main
##################################################################

die "Target folder exists: $targetFolder" if -e $targetFolder;

process_dir("./webim", $targetFolder);

chdir $targetFolder;
`zip -r ../webim$suffix.zip *`;
