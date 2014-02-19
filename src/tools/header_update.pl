#!/usr/bin/perl
=head1 cli-script for headers update

 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.

=head2 Usage

header_update.pl [--quiet] --dir <directory> --header <header file>
[--exclude-dir <directory> ...]

header_update.pl --help

=head2 Example

header_update.pl --dir ../mibew --header header.txt
=cut

use strict;
use warnings;

use Getopt::Long qw(:config no_ignore_case bundling no_auto_abbrev);

my $options = {};
my @exclude_dirs;

# get and check specified cli options
unless ( GetOptions( $options,
		     'help|h',
		     'quiet|q',
		     'dir=s',
		     'header=s',
		     'exclude-dir=s' => \@exclude_dirs ) &&
	 scalar(keys(%$options)) &&
	 ( !$options->{'help'} &&
	   ($options->{'dir'} &&
	    $options->{'header'}) ) ) {

    print STDERR "Usage:\t$0 [--quiet] --dir <directory> " .
		 "--header <header file> [--exclude-dir <directory> ...]" .
		 "\n\t$0 --help\n\n" .
		 "Example: $0 --dir ../mibew --header header.txt\n\n\n";
    exit(1);

}
$options->{'quiet'} ||= 0;

# get new header as a raw text
unless (open(IN, '<', $options->{'header'})) {
    print STDERR "[fatal] Can't read header file: $!\n";
    exit(1);
}

# transform header into a valid comment
print "[info] Reading header file\n" unless $options->{'quiet'};
my $header = "/*\n";
while(<IN>) {
    chomp;
    $header .= $_ ? " * $_\n" : " *\n";
}
close(IN);
$header .= " */";

# fix paths of the excluded directories
map { $_ = fix_dir_name($_) } @exclude_dirs;

# update headers
update_dir($options->{'dir'}, $header, \@exclude_dirs, $options->{'quiet'});

##############################################################################

# Function: update_dir
# Description
#       Recursive function to update headers of all files in the directory and
#       all of its subdirectories
# Argument(s)
#       1. (string) name of the directory
#       2. (string) new header as a valid comment
#       3. (link to array) list of excluded directories that should be skipped
#       4. (boolean) flag of the quiet mode
# Returns
#       1 on success or 0 on error
sub update_dir {
    my $dir = shift;
    my $header = shift;
    my $exclude_dirs = shift;
    my $quiet = shift || 0;

# fix path of the directory
    $dir = fix_dir_name($dir);

# check whether the directory is excluded and should be skipped
    foreach (@$exclude_dirs) {
	if ($dir eq $_) {
	    print "[info] Skipped excluded directory $dir\n" unless $quiet;
	    return 0;
	}
    }

# proceed with the directory
    my $handler;
    unless (opendir($handler, $dir)) {
	print STDERR "[warn] Can't open directory $dir: $!\n";
	return 0;
    }
    while (my $item = readdir($handler)) {
	next if ($item =~ /^\.*$/);
	if (-d $dir . '/' . $item) {
# proceed with a subdirectory
	    print "[info] Processing directory $dir/$item\n" unless $quiet;
	    update_dir($dir . '/' . $item, $header, $exclude_dirs, $quiet);
	}
	else {
# proceed with a file
	    print "[info] Updating file $dir/$item\n" unless $quiet;
	    update_file($dir . '/' . $item, $header, $quiet);
	}
    }
    close($handler);
    return 1;
}

# Function: update_file
# Description
#       Function to update header of the (php) file
# Argument(s)
#       1. (string) name of the file
#       2. (string) new header as a valid comment
#       3. (boolean) flag of the quiet mode
# Returns
#       1 on success or 0 on error
sub update_file {
    my $file = shift;
    my $header = shift;
    my $quiet = shift || 0;

# check file's extension
    unless ($file =~ /\.php$/) {
	print "[info] Skipped file $file: not a php script\n" unless $quiet;
	return 0;
    }

# try to get the contents of the file
    my $content = get_file_content($file);
    unless ($content->{'success'}) {
	print "[info] Skipped file $file: unable to get content\n" unless $quiet;
	return 0;
    }

# check file's content to be a php code
    unless ($content->{'content'} =~ /^<\?php/) {
	print "[info] Skipped file $file: not a php script\n" unless $quiet;
	return 0;
    }

# update header
    $content->{'content'} =~ s~^(<\?php\n)/\*.*?\*/~$1$header~s;

# try to set the new contents of the file
    if (set_file_content($file, $content->{'content'})) {
	print "[info] Successfully updated file $file\n" unless $quiet;
	return 1;
    }
    else {
	print "[info] Failed to update file $file\n" unless $quiet;
	return 0;
    }
}

# Function: get_file_content
# Description
#       Function to get a content of a file
# Argument(s)
#       1. (string) name of the file
# Returns
#       (link to hash) result as a hash:
#
#              { 'success' => 1 on success or 0 on error,
#                'content' => <content of the file> on success or
#                             empty string on error }
sub get_file_content {
    my $file = shift;
    unless (open(IN, '<', $file)) {
	print STDERR "[warn] Can't read file $file: $!\n";
	return { 'success' => 0,
		 'content' => '' };
    }
    my @content = <IN>;
    close(IN);
    return { 'success' => 1,
	     'content' => join('', @content) };
}

# Function: set_file_content
# Description
#       Function to set a content for a file
# Argument(s)
#       1. (string) name of the file
#       2. (string) new content
# Returns
#       1 on success or 0 on error
sub set_file_content {
    my $file = shift;
    my $content = shift;
    unless (open(OUT, '>', $file)) {
	print STDERR "[warn] Can't write file $file: $!\n";
	return 0;
    }
    print OUT $content;
    close(OUT);
    return 1;
}

# Function: fix_dir_name
# Description
#       Function to fix a full name of a directory, i.e. to remove the double
#       slashes and a trail slash
# Argument(s)
#       1. (string) full name of a directory
# Returns
#       (string) fixed full name of a directory
sub fix_dir_name {
    my $dir = shift;
    $dir =~ s~/{2,}~/~g;
    $dir =~ s~/$~~;
    return $dir;
}
