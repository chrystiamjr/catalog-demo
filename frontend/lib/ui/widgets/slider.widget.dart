import 'package:another_xlider/another_xlider.dart';
import 'package:flutter/material.dart';

class SliderWidget extends StatelessWidget {
  final int currValue;
  final Function(String, int) action;
  final List<String> items;

  const SliderWidget({Key? key, required this.currValue, required this.action, required this.items}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return FlutterSlider(
      handlerHeight: 30,
      jump: true,
      trackBar: const FlutterSliderTrackBar(activeTrackBar: BoxDecoration(color: Colors.deepPurple)),
      onDragCompleted: (_, high, __) {
        final index = items.indexOf(high);
        var percentage = ((index / items.length) * 100).ceil();
        percentage = index == items.length - 1 ? 100 : percentage;
        action(high, percentage);
      },
      fixedValues: List.generate(
        items.length,
        (index) {
          final percentage = ((index / items.length) * 100).ceil();
          return FlutterSliderFixedValue(
            percent: index == items.length - 1 ? 100 : percentage,
            value: items[index],
          );
        },
      ),
      values: [currValue.toDouble()],
    );
  }
}
