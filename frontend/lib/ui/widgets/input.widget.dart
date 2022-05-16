import 'package:flutter/material.dart';

class Input extends StatelessWidget {
  final IconData? icon;
  final String? label;
  final String? hint;
  final Color? background;
  final TextEditingController? controller;
  final double? width;
  final Function()? onBlur;
  final Function(String)? onChanged;
  final EdgeInsets? padding;
  final EdgeInsets? margin;
  final TextInputType type;
  final bool isReadOnly;
  final bool isPwd;

  const Input({
    Key? key,
    this.icon,
    this.label,
    this.hint,
    this.background,
    this.width,
    this.controller,
    this.onChanged,
    this.onBlur,
    this.margin,
    this.padding,
    this.type = TextInputType.text,
    this.isReadOnly = false,
    this.isPwd = false,
  }) : super(key: key);

  InputDecoration _getInputdecoration() {
    if (icon == null) {
      return InputDecoration(
        labelText: label,
        hintText: hint,
        border: InputBorder.none,
        contentPadding: const EdgeInsets.all(0),
      );
    } else {
      return InputDecoration(
        icon: Icon(icon),
        labelText: label,
        hintText: hint,
        border: InputBorder.none,
        contentPadding: const EdgeInsets.all(0),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final double left = icon == null ? 15 : 10;
    final double right = icon == null ? 15 : 20;

    return Container(
      margin: margin,
      padding: padding ?? EdgeInsets.fromLTRB(left, 4, right, 4),
      width: width ?? 250,
      decoration: BoxDecoration(
        color: background ?? Colors.white,
        borderRadius: const BorderRadius.all(Radius.circular(25)),
      ),
      child: Focus(
        child: TextField(
          obscureText: isPwd,
          controller: controller,
          keyboardType: type,
          readOnly: isReadOnly,
          decoration: _getInputdecoration(),
          onChanged: (val) => onChanged?.call(val),
        ),
        onFocusChange: (hasFocus) => onBlur?.call(),
      ),
    );
  }
}
