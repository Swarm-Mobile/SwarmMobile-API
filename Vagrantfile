# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
    config.vm.box_url = "http://files.vagrantup.com/precise32.box"
    config.vm.box = "precise32"
    config.vm.network :private_network, ip: "192.168.44.100"
    config.vm.synced_folder "./", "/vagrant", owner: "www-data", group: "www-data"
    config.vm.provision :shell do |shell|
            shell.path = "provision.sh"
    end
    config.vm.provider :virtualbox do |virtualbox|       
      virtualbox.customize ["modifyvm", :id, "--memory", "4096"]        
      virtualbox.customize ["modifyvm", :id, "--cpuexecutioncap", "50"]
   end
end