#
# Makefile to make UFO font.
#
# @author akahuku@gmail.com
# @license GPL v2.0
#

SHELL = /bin/sh

VERSION = $(shell echo -n `git describe --tags --abbrev=0|sed -e 's/[^0-9.]//g'`.`git rev-list --count HEAD`)
COPYRIGHT = "Licensed under the GNU GPL v2.0 with font embedding exception"

SRC_DIR = src
BUILD_DIR = build
BIN_DIR = bin
GRYPH_DIR = gryph
UNIFONT_GRYPH_DIR =   $(GRYPH_DIR)/00-unifont
WQY_GRYPH_DIR =       $(GRYPH_DIR)/01-wqy11
SHINONOME_GRYPH_DIR = $(GRYPH_DIR)/02-shinonome14
UFO_GRYPH_DIR =       $(GRYPH_DIR)/05-ufo

ALL_GRYPHS = $(shell find $(GRYPH_DIR)/[0-9]* -type f -name '*.png')

UNICODE_BLOCK_FILE = $(SRC_DIR)/unicode/Blocks.txt
UNICODE_WIDTH_FILE = $(SRC_DIR)/unicode/EastAsianWidth.txt
WQY_BLOCKS = 102,103,105,109,110,111,113,117,151,157
SHINONOME_BLOCKS = 106,107,108,119,160

all: $(BUILD_DIR)/ufo.pcf $(BUILD_DIR)/ufo.png

# rules to build ufo

$(BUILD_DIR)/ufo.pcf: $(BUILD_DIR)/ufo.bdf
	bdftopcf -t $< > $@
	gzip -9c $@ > $@.gz

$(BUILD_DIR)/ufo.png: $(BUILD_DIR)/ufo.hex
	$(BIN_DIR)/hex2png \
		$< $@ --block=$(UNICODE_BLOCK_FILE)

$(BUILD_DIR)/ufo.bdf: $(BUILD_DIR)/ufo.hex
	$(BIN_DIR)/hex2bdf \
		--copyright=$(COPYRIGHT) \
		--version="$(VERSION)" \
		$< > $@
	gzip -9c $@ > $@.gz

$(BUILD_DIR)/ufo.hex: $(ALL_GRYPHS)
	$(BIN_DIR)/gryph2hex \
		--gryph=$(GRYPH_DIR) \
		--block=$(UNICODE_BLOCK_FILE) \
		> $@

# individual rules

unifont-gryph: FORCE
	$(BIN_DIR)/hex2gryph \
		--hex=$(SRC_DIR)/hex/unifont \
		--gryph=$(UNIFONT_GRYPH_DIR) \
		--block=$(UNICODE_BLOCK_FILE)

wqy-gryph: FORCE
#	$(BIN_DIR)/png2hex \
#		--png=$(SRC_DIR)/wqy/wqy-11pt.png \
#		--bdf=$(SRC_DIR)/wqy/wenquanyi_11pt.bdf \
#		--hex=$(SRC_DIR)/hex/wqy/wenquanyi_11pt.hex \
#		--cell=18,20 --offset=2,2

	$(BIN_DIR)/hex2gryph \
		--hex=$(SRC_DIR)/hex/wqy \
		--gryph=$(WQY_GRYPH_DIR) \
		--block=$(UNICODE_BLOCK_FILE) \
		--block-include=$(WQY_BLOCKS) \
		--transparent

shinonome-gryph: FORCE
#	$(BIN_DIR)/png2hex \
#		--png=$(SRC_DIR)/shinonome/shinonome-14px.png \
#		--bdf=$(SRC_DIR)/shinonome/shnmk14.bdf \
#		--hex=$(SRC_DIR)/hex/shinonome/shnmk14.hex \
#		--translate=$(SRC_DIR)/translate-data/jisx0213-2004-8bit-std.txt \
#		--cell=16,16 --offset=1,1

	$(BIN_DIR)/hex2gryph \
		--hex=$(SRC_DIR)/hex/shinonome \
		--gryph=$(SHINONOME_GRYPH_DIR) \
		--block=$(UNICODE_BLOCK_FILE) \
		--block-include=$(SHINONOME_BLOCKS) \
		--transparent

check-width: FORCE
	$(BIN_DIR)/checkwidth \
		--hex=$(SRC_DIR)/hex/unifont \
		--block=$(UNICODE_BLOCK_FILE) \
		--width=$(UNICODE_WIDTH_FILE)

# phony rules

FORCE:

clean: FORCE
	-rm $(BUILD_DIR)/ufo.hex
	-rm $(BUILD_DIR)/ufo.bdf
	-rm $(BUILD_DIR)/ufo.pcf
	-rm $(BUILD_DIR)/ufo.bdf.gz
	-rm $(BUILD_DIR)/ufo.pcf.gz
	-rm $(BUILD_DIR)/ufo.png

install: FORCE
	cp $(BUILD_DIR)/ufo.pcf ~/.fonts/
	fc-cache -f

.PHONY: all clean install
