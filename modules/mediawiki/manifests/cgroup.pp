# Class: mediawiki::cgroup

class mediawiki::cgroup {

    ensure_packages('cgroup-bin')

    # The cgroup-mediawiki-clean script is used as the release_agent
    # script for the cgroup. When the last task in the cgroup exits,
    # the kernel will run the script.

    file { '/usr/local/bin/cgroup-mediawiki-clean':
        source => 'puppet:///modules/mediawiki/cgroup/cgroup-mediawiki-clean',
        owner  => 'root',
        group  => 'root',
        mode   => '0755',
    }

    systemd::service { 'mw-cgroup':
        ensure  => present,
        content => systemd_template('mw-cgroup'),
        restart => false,
    }

    grub::bootparam { 'cgroup_enable':
        value => 'memory',
    }

    grub::bootparam { 'swapaccount':
        value => '1',
    }

    # Disable cgroup memory accounting
    grub::bootparam { 'cgroup.memory':
        value => 'nokmem',
    }
}
