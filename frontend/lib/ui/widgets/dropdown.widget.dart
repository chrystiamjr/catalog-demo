import 'package:flutter/material.dart';
import 'package:frontend/enum/dropdown_style.enum.dart';
import 'package:frontend/util/widget.functions.dart';

import 'label.widget.dart';

class DropdownWidget extends StatelessWidget {
  String? actualValue;
  Function(String?)? onChange;
  String label;
  Map<String, String> items;
  Color color;
  DropdownStyle displayStyle;

  DropdownWidget({
    Key? key,
    this.actualValue,
    this.onChange,
    this.label = 'Select an option:',
    this.items = const {},
    this.color = Colors.deepPurple,
    this.displayStyle = DropdownStyle.horizontal,
  }) : super(key: key);

  List<DropdownMenuItem<String>> _optionList() {
    if (items.entries.isEmpty) {
      return List.generate(1, (index) => DropdownMenuItem(value: '', child: Label('----')));
    }
    return items.entries
        .map<DropdownMenuItem<String>>((MapEntry item) => DropdownMenuItem(
            value: item.key,
            child: Label(
              item.value,
              size: 12,
              weight: FontWeight.normal,
            )))
        .toList();
  }

  Widget _label() {
    if (displayStyle == DropdownStyle.horizontal) {
      return Label(label);
    } else {
      return Padding(padding: const EdgeInsets.only(bottom: 5), child: Label(label));
    }
  }

  List<Widget> getChildren() {
    return [
      Container(alignment: Alignment.centerLeft, child: _label()),
      widgetValidator(
        validation: displayStyle == DropdownStyle.horizontal,
        child: const Spacer(),
      ),
      DropdownButton<String>(
        isExpanded: true,
        value: actualValue,
        icon: const Icon(Icons.arrow_drop_down),
        iconSize: 24,
        elevation: 16,
        style: TextStyle(color: color),
        underline: Container(height: 2, color: color),
        onChanged: onChange,
        items: _optionList(),
      )
    ];
  }

  @override
  Widget build(BuildContext context) {
    return Container(
        child: (displayStyle != DropdownStyle.horizontal)
            ? Column(mainAxisSize: MainAxisSize.min, children: getChildren())
            : Row(
                mainAxisSize: MainAxisSize.max,
                mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                crossAxisAlignment: CrossAxisAlignment.center,
                children: getChildren(),
              ));
  }
}
