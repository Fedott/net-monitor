/// <reference path="../typings/browser/ambient/react/react.d.ts" />

import * as React from 'react'

interface Ip {
    ip: string;
}

export interface IpItemState {}

export interface IpItemProps {
    item: Ip;
    ping: boolean;
}

export class IpListItem extends React.Component<IpItemProps, IpItemState> {
    constructor() {
        super();
        
        this.startPing.bind(this);
    }

    startPing() {

    }

    render() {
        return (
            <li className="mdl-list__item">
                <div className="mdl-list__item-primary-content">
                    {this.props.item.ip}
                </div>
                <span className="mdl-list__item-secondary-action">
                    <label className="mdl-switch mdl-js-switch mdl-js-ripple-effect">
                        <input type="checkbox" className="mdl-switch__input"/>
                        <span className="mdl-switch__label"/>
                    </label>
                </span>
            </li>
        );
    }
}
