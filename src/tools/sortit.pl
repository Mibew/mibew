#!/usr/bin/perl

sub sort_transl($) {
	my($from) = @_;
	my @translation = ();
	my $header = "";
	open(IN, "$from");
	while(<IN>) {
		chomp;
		my $curr = $_;
		if(/^([\w\.]+)=(.*)$/) {
			if($1 ne "encoding" && $1 ne "output_charset" && $1 ne "output_encoding") {
				push @translation, $curr;
			} else {
			    $header .= "$curr\n";
			}
		} else {
			die "wrong line in $from: $curr\n";
		}
	}
	close(IN);
	open(OUT, "> $from");
	print OUT $header;
	for$line(sort @translation) {
	    print OUT "$line\n";
	}
	close(OUT);
}

die "no parameter\n" if $#ARGV < 0;
die "doesn't exists\n" unless -e $ARGV[0];

sort_transl($ARGV[0]);
