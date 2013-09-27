#!/usr/bin/perl

sub read_transl($) {
	my($from) = @_;
	my %translation = ();
	open(IN, "mibew/locales/$from/properties");
	while(<IN>) {
		chomp;
		if(/^([\w\.]+)=(.*)$/) {
			if($1 ne "encoding" && $1 ne "output_charset" && $1 ne "output_encoding") {
				$translation{$1} = $2;
			}
		} else {
			die "wrong line in $from: $_\n";
		}
	}
	close(IN);
	return %translation;
}

%tr_en = read_transl("en");

@all_keys = keys %tr_en;

sub check_transl($%) {
	my($name,%tr) = @_;
	print "checking $name...\n";
	my @totransl = ();
	for $key (@all_keys) {
		unless(exists $tr{$key}) {
			push @totransl, "$key=".$tr_en{$key};
		}
	}
	for $key(keys %tr) {
		unless(exists $tr_en{$key}) {
			print "unknown key in $name: $key\n";
		}
	}
	if($#totransl >= 0) {
		print "@{[$#totransl+1]} lines absent in locales/$name/properties\n";
		open(OUT, "> absent_$name");
		for(sort @totransl) {
			print OUT "$_\n";
		}
		close(OUT);
	}
}

check_transl("ru", read_transl("ru"));
check_transl("fr", read_transl("fr"));
check_transl("de", read_transl("de"));
check_transl("sp", read_transl("sp"));
check_transl("it", read_transl("it"));
check_transl("pl", read_transl("pl"));
check_transl("zh-tw", read_transl("zh-tw"));

