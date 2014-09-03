# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
config.vm.box_url = "https://cloud-images.ubuntu.com/vagrant/trusty/current/trusty-server-cloudimg-amd64-vagrant-disk1.box"
config.vm.box = "trusty64"
config.vm.network :private_network, ip: "192.168.44.100"
config.vm.synced_folder "./", "/vagrant", owner: "www-data", group: "www-data"
config.vm.provision :shell do |shell|
    shell.path = "provision.sh"
end
end