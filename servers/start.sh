#!/bin/sh
apache2-foreground
service metricbeat start
service filebeat start