#!/usr/bin/perl

##################################################################
# Arguments
##################################################################

$targetFolder = "deploy";
$suffix = "165";

##################################################################
# Copies tree into target folder, preprocess .phps
##################################################################

sub process_dir($$) {
	my ($from,$to) = @_;
	opendir(DIR, $from) || die "can't opendir $from: $!";
    my @content = readdir(DIR);
    closedir DIR;
    mkdir $to;
    
	for(grep { -f "$from/$_" && ($_ !~ /^\./ || $_ eq ".htaccess" || $_ eq ".keep") } @content) {
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

`rm -rf $targetFolder`;
die "Target folder exists: $targetFolder" if -e $targetFolder;

process_dir("./mibew", $targetFolder);

`rm -rf release$suffix`;
die "release folder exists: release$suffix" if -e "release$suffix";
mkdir "release$suffix";

chdir "$targetFolder";

`zip -r ../release$suffix/mibew${suffix}_all.zip * .htaccess`;

chdir "locales";

foreach $locale qw ( ar be bg ca cs da de el fa fi fr he hr hu id it ka lv nl pl pt-br pt-pt ro ru sp sv th tr ua zh-cn zh-tw ) {

    `zip -r ../../release$suffix/mibew${suffix}_$locale.zip $locale`;
    `rm -rf $locale`;

}

chdir "..";
`zip -r ../release$suffix/mibew$suffix.zip * .htaccess`;

chdir "..";
`rm -rf $targetFolder`;
