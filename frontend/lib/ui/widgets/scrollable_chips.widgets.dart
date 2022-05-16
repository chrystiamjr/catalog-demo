import 'package:flutter/material.dart';
import 'package:frontend/ui/widgets/label.widget.dart';
import 'package:frontend/util/screen_util.dart';

class ScrollableSchips extends StatelessWidget {
  final List<String> items;
  final double? width;
  final ScrollController _controller = ScrollController();

  ScrollableSchips({Key? key, required this.items, this.width}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: width ?? w(320),
      height: 30,
      child: Scrollbar(
        thumbVisibility: true,
        controller: _controller,
        scrollbarOrientation: ScrollbarOrientation.bottom,
        child: ListView.builder(
            shrinkWrap: true,
            controller: _controller,
            scrollDirection: Axis.horizontal,
            itemCount: items.length,
            itemBuilder: (_, i) {
              return Padding(
                padding: const EdgeInsets.only(right: 8),
                child: Chip(
                  label: Label(items[i], size: 9),
                ),
              );
            }),
      ),
    );
  }
}
