#!/usr/bin/perl

##################################################################
# Arguments
##################################################################

$targetFolder = "deploy";
$suffix = "161";

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

process_dir("./webim", $targetFolder);

chdir "$targetFolder/locales";

`zip -r ../../webim${suffix}_de.zip de`;
`rm -rf de`;

`zip -r ../../webim${suffix}_ru.zip ru`;
`rm -rf ru`;

`zip -r ../../webim${suffix}_fr.zip fr`;
`rm -rf fr`;

`zip -r ../../webim${suffix}_it.zip it`;
`rm -rf it`;

`zip -r ../../webim${suffix}_lv.zip lv`;
`rm -rf lv`;

`zip -r ../../webim${suffix}_pl.zip pl`;
`rm -rf pl`;

`zip -r ../../webim${suffix}_pt-br.zip pt-br`;
`rm -rf pt-br`;

`zip -r ../../webim${suffix}_sp.zip sp`;
`rm -rf sp`;

`zip -r ../../webim${suffix}_tr.zip tr`;
`rm -rf tr`;

`zip -r ../../webim${suffix}_ua.zip ua`;
`rm -rf ua`;

`zip -r ../../webim${suffix}_he.zip he`;
`rm -rf he`;

`zip -r ../../webim${suffix}_hr.zip hr`;
`rm -rf hr`;

`zip -r ../../webim${suffix}_zh-cn.zip zh-cn`;
`rm -rf zh-cn`;

`zip -r ../../webim${suffix}_zh-tw.zip zh-tw`;
`rm -rf zh-tw`;

chdir "..";
`zip -r ../webim$suffix.zip *`;
